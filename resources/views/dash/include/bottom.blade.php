<script src="{{ asset('dashboard_assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/chartjs.min.js') }}"></script>

<!-- Argon Dashboard Control Center -->
<script src="{{ asset('dashboard_assets/js/argon-dashboard.min.js') }}"></script>

<!-- External Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

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