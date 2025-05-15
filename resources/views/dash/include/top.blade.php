<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @yield('styles')

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard_assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('dashboard_assets/img/logo.png') }}">

    <!-- Title -->
    <title>@yield('title', 'Dashboard')</title>

    <!-- Fonts and Icons -->
    <link href="{{ asset('dashboard_assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('dashboard_assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <!-- Main CSS -->
    <link id="pagestyle" href="{{ asset('dashboard_assets/css/argon-dashboard.css') }}" rel="stylesheet" />

    <!-- External Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        :root {
            --primary-color: #7367f0;
            --primary-light: rgba(115, 103, 240, 0.12);
            --secondary-color: #82868b;
            --danger-color: #ea5455;
            --success-color: #28c76f;
            --warning-color: #ff9f43;
            --dark-color: #4b4b4b;
            --light-color: #f8f8f8;
            --border-color: rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* تحسينات للعرض على الهواتف */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1030;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .mobile-menu-btn {
                display: flex !important;
            }
        }
    </style>
</head>