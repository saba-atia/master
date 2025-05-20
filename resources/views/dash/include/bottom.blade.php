<script src="{{ asset('dashboard_assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/chartjs.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-hijri/2.1.2/moment-hijri.min.js"></script>

<!-- Argon Dashboard Control Center -->
<script src="{{ asset('dashboard_assets/js/argon-dashboard.min.js') }}"></script>

<!-- External Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // تهيئة العناصر بعد تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة السكربتات هنا
        if (typeof PerfectScrollbar !== 'undefined') {
            const ps = new PerfectScrollbar('.nav-container');
        }
        
        // ضبط ارتفاع العناصر
        function setHeights() {
            const windowHeight = window.innerHeight;
            const navContainer = document.querySelector('.nav-container');
            if (navContainer) {
                navContainer.style.maxHeight = (windowHeight - 200) + 'px';
            }
        }
        
        window.addEventListener('resize', setHeights);
        setHeights();
    });
</script>

@yield('scripts')