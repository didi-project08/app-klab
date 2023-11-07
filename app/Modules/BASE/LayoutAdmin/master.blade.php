<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities. laravel/framework: ^8.40">
    <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    <title>@yield('title') | {{ env('SYS_NAME') }}</title>
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    @includeIf('BASE.LayoutAdmin.partials.css')

    <link rel="stylesheet" type="text/css" href="{{asset('assets/sweetalert2/sweetalert2.min.css')}}">
    <script src="{{asset('assets/sweetalert2/sweetalert2.all.min.js')}}"></script>
  </head>
  <body>

    <div class="preloader" style="display:none">
      <div align="center" class="preloaderContent">
        <img src="{{ asset('assets') }}/loading.gif" class="logo_client" style="height:130px;">
      </div>
    </div>
    <div class="preloader2" style="display:none !important">
      <div align="center" class="preloader2Content">
        <img src="{{ asset('assets') }}/loading.gif" class="logo_client" style="height:130px;">
      </div>
    </div>

    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="theme-loader"></div>
    </div>
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-sidebar" id="pageWrapper">
      <!-- Page Header Start-->
      @includeIf('BASE.LayoutAdmin.partials.header')
      <!-- Page Header Ends -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper sidebar-icon">
        <!-- Page Sidebar Start-->
        @includeIf('BASE.LayoutAdmin.partials.sidebar')
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <!-- Container-fluid starts-->
          @yield('content')
          <!-- Container-fluid Ends-->
        </div>

        <!-- footer start-->
        <!-- <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 footer-copyright">
                <p class="mb-0">Copyright 2023 © SIM Medical Check Up.</p>
              </div>
              <div class="col-md-6">
                <p class="pull-right mb-0"><i>Version : 23.03.10</i></p>
              </div>
            </div>
          </div>
        </footer> -->

      </div>
    </div>
    <!-- latest jquery-->
    @includeIf('BASE.LayoutAdmin.partials.js')
  </body>
</html>