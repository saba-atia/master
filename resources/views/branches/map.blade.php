@extends('dash.dash')

@section('contentdash')
<div class="container-fluid py-4">
    <!-- قسم الخريطة المعدل -->
    <div class="position-relative mt-3">
        <!-- شاشة التحميل -->
        <div id="map-loading" class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="z-index: 1000; border-radius: 12px;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0" id="loading-text">جاري تحميل الخريطة...</p>
                <div id="fallback-ui" style="display:none;" class="mt-3">
                    <button onclick="initOSMMap()" class="btn btn-sm btn-warning">
                        <i class="material-icons">map</i> استخدام خريطة مبسطة
                    </button>
                    <button onclick="retryLoading()" class="btn btn-sm btn-info ms-2">
                        <i class="material-icons">refresh</i> إعادة المحاولة
                    </button>
                </div>
            </div>
        </div>
        
        <!-- حاويات الخرائط -->
        <div id="google-map" style="height: 500px; position: relative; z-index: 0; display:none;"></div>
        <div id="osm-map" style="height: 500px; display: none;"></div>
    </div>
</div>

<script>
// 1. المتغيرات العامة
let mapLoaded = false;
const MAX_RETRIES = 3;
let retryCount = 0;
let currentMapType = null;
let currentMapInstance = null;
let currentMarker = null;

// 2. تهيئة الخريطة الرئيسية
function initMapSystem() {
    // المحاولة الأولى مع Google Maps
    loadGoogleMapsAPI();
    
    // إظهار خيار Fallback بعد 10 ثواني إذا لم يتم التحميل
    setTimeout(() => {
        if (!mapLoaded) {
            document.getElementById('fallback-ui').style.display = 'block';
            document.getElementById('loading-text').textContent = 'التحميل يأخذ وقتًا أطول من المتوقع...';
        }
    }, 10000);
}

// 3. تحميل Google Maps API
function loadGoogleMapsAPI() {
    const API_KEY = '{{ env("GOOGLE_MAPS_API_KEY") }}';
    
    if (checkGoogleMapsLoaded()) {
        initGoogleMaps();
        return;
    }
    
    document.getElementById('loading-text').textContent = 'جاري تحميل خرائط جوجل...';
    
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${API_KEY}&callback=onGoogleMapsLoaded`;
    script.async = true;
    script.defer = true;
    script.onerror = () => handleMapLoadError('فشل تحميل سكريبت خرائط جوجل');
    
    document.head.appendChild(script);
    
    // Timeout للتحقق من التحميل
    setTimeout(() => {
        if (!checkGoogleMapsLoaded() && !mapLoaded) {
            handleMapLoadError('انتهى وقت انتظار تحميل خرائط جوجل');
        }
    }, 8000);
}

function onGoogleMapsLoaded() {
    initGoogleMaps();
}

// 4. تهيئة Google Maps
function initGoogleMaps() {
    try {
        if (currentMapType === 'google') return;
        cleanupPreviousMap();
        
        document.getElementById('loading-text').textContent = 'جاري تهيئة خرائط جوجل...';
        document.getElementById('google-map').style.display = 'block';
        
        const firstBranch = document.getElementById('branch-select').options[0];
        const initialLat = parseFloat(firstBranch.getAttribute('data-lat')) || 24.7136;
        const initialLng = parseFloat(firstBranch.getAttribute('data-lng')) || 46.6753;
        
        const mapOptions = {
            center: { lat: initialLat, lng: initialLng },
            zoom: 15,
            disableDefaultUI: false,
            gestureHandling: 'cooperative'
        };
        
        currentMapInstance = new google.maps.Map(document.getElementById('google-map'), mapOptions);
        currentMarker = new google.maps.Marker({
            position: { lat: initialLat, lng: initialLng },
            map: currentMapInstance,
            draggable: true,
            title: 'اسحب العلامة لتغيير الموقع'
        });
        
        currentMarker.addListener('dragend', function() {
            updateFormFields(
                currentMarker.getPosition().lat(),
                currentMarker.getPosition().lng()
            );
        });
        
        currentMapType = 'google';
        finishMapLoading();
    } catch (error) {
        handleMapLoadError(`خطأ في تهيئة خرائط جوجل: ${error.message}`);
    }
}

// 5. تهيئة OpenStreetMap
function initOSMMap() {
    try {
        if (currentMapType === 'osm') return;
        cleanupPreviousMap();
        
        document.getElementById('loading-text').textContent = 'جاري تحميل الخريطة المبسطة...';
        document.getElementById('fallback-ui').style.display = 'none';
        
        const googleMapElement = document.getElementById('google-map');
        const osmMapElement = document.getElementById('osm-map');
        
        googleMapElement.style.display = 'none';
        osmMapElement.style.display = 'block';
        
        // تحميل موارد Leaflet إذا لم تكن محملة
        if (!window.L) {
            loadLeafletResources(() => createOSMMap());
        } else {
            createOSMMap();
        }
    } catch (error) {
        handleMapLoadError(`خطأ في تحويل الخريطة إلى النسخة المبسطة: ${error.message}`);
    }
}

function loadLeafletResources(callback) {
    // تحميل CSS
    if (!document.querySelector('link[href*="leaflet"]')) {
        const leafletCSS = document.createElement('link');
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
        document.head.appendChild(leafletCSS);
    }
    
    // تحميل JS
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
    script.onload = callback;
    script.onerror = () => handleMapLoadError('فشل تحميل سكريبت Leaflet');
    document.head.appendChild(script);
}

function createOSMMap() {
    try {
        const firstBranch = document.getElementById('branch-select').options[0];
        const initialLat = parseFloat(firstBranch.getAttribute('data-lat')) || 24.7136;
        const initialLng = parseFloat(firstBranch.getAttribute('data-lng')) || 46.6753;
        
        currentMapInstance = L.map('osm-map').setView([initialLat, initialLng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(currentMapInstance);
        
        currentMarker = L.marker([initialLat, initialLng], {
            draggable: true,
            title: 'اسحب العلامة لتغيير الموقع'
        }).addTo(currentMapInstance);
        
        currentMarker.on('dragend', function() {
            const position = currentMarker.getLatLng();
            updateFormFields(position.lat, position.lng);
        });
        
        currentMapType = 'osm';
        finishMapLoading();
    } catch (error) {
        handleMapLoadError(`خطأ في إنشاء خريطة OSM: ${error.message}`);
    }
}

// 6. وظائف مساعدة
function checkGoogleMapsLoaded() {
    return window.google && window.google.maps;
}

function cleanupPreviousMap() {
    if (currentMapType === 'google') {
        // لا يوجد طريقة رسمية لتنظيف خرائط جوجل
        document.getElementById('google-map').innerHTML = '';
    } else if (currentMapType === 'osm' && currentMapInstance) {
        currentMapInstance.remove();
    }
    
    currentMapInstance = null;
    currentMarker = null;
}

function updateFormFields(lat, lng) {
    document.getElementById('form-latitude').value = lat;
    document.getElementById('form-longitude').value = lng;
}

function finishMapLoading() {
    mapLoaded = true;
    document.getElementById('map-loading').style.display = 'none';
    console.log(`تم تحميل الخريطة بنجاح (${currentMapType})`);
}

function handleMapLoadError(message) {
    console.error(message);
    retryCount++;
    
    const loadingText = document.getElementById('loading-text');
    const fallbackUI = document.getElementById('fallback-ui');
    
    if (retryCount <= MAX_RETRIES && !mapLoaded) {
        loadingText.textContent = `إعادة المحاولة... (${retryCount}/${MAX_RETRIES})`;
        setTimeout(() => retryMapLoad(), 2000);
    } else {
        loadingText.innerHTML = 'فشل تحميل الخريطة. <br>الرجاء التحقق من اتصال الإنترنت أو المحاولة لاحقًا.';
        fallbackUI.style.display = 'block';
    }
}

function retryMapLoad() {
    if (retryCount % 2 === 0) {
        initOSMMap();
    } else {
        loadGoogleMapsAPI();
    }
}

function retryLoading() {
    retryCount = 0;
    mapLoaded = false;
    document.getElementById('map-loading').style.display = 'flex';
    document.getElementById('fallback-ui').style.display = 'none';
    loadGoogleMapsAPI();
}

// بدء التحميل عند اكتمال الصفحة
document.addEventListener('DOMContentLoaded', initMapSystem);
</script>

<style>
    #google-map, #osm-map {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    
    .leaflet-container {
        height: 100%;
        width: 100%;
        border-radius: 12px;
    }
    
    #map-loading {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        backdrop-filter: blur(2px);
    }
    
    .material-icons {
        vertical-align: middle;
        margin-bottom: 2px;
    }
</style>
@endsection