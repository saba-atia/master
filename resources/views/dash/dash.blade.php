<!--
=========================================================
* Argon Dashboard 3 - v2.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

@include('dash.include.top')

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-dark position-absolute w-100"></div>
  @include('dash.include.side')
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
   @include('dash.include.nav')
    <!-- End Navbar -->
   @yield('contentdash')
  </main>
  @include('dash.include.rightside')
  <!--   Core JS Files   -->
  @include('dash.include.footer')

  @include('dash.include.bottom')

</body>

</html>