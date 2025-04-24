<!DOCTYPE html>
<html lang="en">

@include('home.include.top')

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    @include('home.include.navbar')
  </header>

  <main class="main">

    <!-- Hero Section -->
 
@yield('content')

   

    <!-- Contact Section -->
    {{-- @include('layouts.contactsection') --}}
        <!-- Contact Section -->
      @include('home.include.footer')
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
@include('home.include.bottom')

</body>

</html>