<!DOCTYPE html>
<html lang="en">
<head>
    @include('dash.include.top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* تنسيقات عامة لضمان العرض الصحيح */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body.g-sidenav-show {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <!-- السايد بار -->
    @include('dash.include.side')

    <!-- المحتوى الرئيسي -->
    <main class="main-content position-relative">
        <div class="container-fluid py-4">
            @yield('contentdash')
        </div>
    </main>

    @include('dash.include.bottom')
</body>
</html>