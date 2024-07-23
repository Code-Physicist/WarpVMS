<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('') }}" />

    <title>@yield('title')</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}" />

    <!-- Icomoon Font Icons css -->
    <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @yield('style')
    
  </head>

  <body>
    <!-- Page wrapper start -->
    <div class="page-wrapper">

      <!-- Main container start -->
      <div class="main-container">

        <!-- Sidebar wrapper start -->
        <nav id="sidebar" class="sidebar-wrapper">

          <!-- App brand starts -->
          <div class="app-brand px-3 py-2 d-flex align-items-center">
            <a href="index.html">
              <img src="{{ asset('images/BTSVISION_Combined_White.svg') }}" class="logo" alt="Bootstrap Gallery" />
            </a>
          </div>
          <!-- App brand ends -->

          <!-- Sidebar menu starts -->
          <div class="sidebarMenuScroll">
            <ul class="sidebar-menu">
              @if(Request::is('admin/dashboard'))
              <li class="active current-page">
              @else
              <li>
              @endif
                <a href="{{url('/admin/dashboard')}}">
                  <i class="icon-roofing"></i>
                  <span class="menu-text">Dashboard</span>
                </a>
              </li>
              @if(Request::is('admin/invitations'))
              <li class="active current-page">
              @else
              <li>
              @endif
                <a href="{{url('/admin/invitations')}}">
                  <i class="icon-calendar"></i>
                  <span class="menu-text">Invitations</span>
                </a>
              </li>
              @if( $dept_level < 2)
                @if(Request::is('admin/departments'))
                  <li class="active current-page">
                @else
                  <li>
                @endif
                    <a href="{{url('/admin/departments')}}">
                      <i class="icon-business"></i>
                      <span class="menu-text">Departments</span>
                    </a>
                  </li>
              @endif
              @if(Request::is('admin/tenants'))
              <li class="active current-page">
              @else
              <li>
              @endif
                <a href="{{url('/admin/tenants')}}">
                  <i class="icon-layers"></i>
                  <span class="menu-text">Tenants</span>
                </a>
              </li>
              @if(Request::is('admin/operators'))
              <li class="active current-page">
              @else
              <li>
              @endif
                <a href="{{url('/admin/operators')}}">
                  <i class="icon-user"></i>
                  <span class="menu-text">Operators</span>
                </a>
              </li>
            </ul>
          </div>
          <!-- Sidebar menu ends -->

        </nav>
        <!-- Sidebar wrapper end -->

        <!-- App container starts -->
        <div class="app-container">

          <!-- App header starts -->
          <div class="app-header d-flex align-items-center">

            <!-- Toggle buttons start -->
            <div class="d-flex">
              <button class="btn btn-outline-success toggle-sidebar" id="toggle-sidebar">
                <i class="icon-menu"></i>
              </button>
              <button class="btn btn-outline-danger pin-sidebar" id="pin-sidebar">
                <i class="icon-menu"></i>
              </button>
            </div>
            <!-- Toggle buttons end -->

            <!-- App brand sm start -->
            <div class="app-brand-sm d-md-none d-sm-block">
              <a href="index.html">
                <img src="{{ asset('images/logo-sm.svg') }}" class="logo" alt="Bootstrap Gallery">
              </a>
            </div>
            <!-- App brand sm end -->
            <div class="dropdown d-flex align-items-center ms-4 pt-2">
                      <h4>{{$dept_name}}</h4>
            </div>

            <!-- App header actions start -->
            <div class="header-actions">
            
              <div class="dropdown ms-3">
                <a class="dropdown-toggle d-flex align-items-center" href="#!" role="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="{{ asset('images/user.png') }}" class="img-3x m-2 ms-0 rounded-5" alt="Admin Templates" />
                  <div class="d-md-flex d-none flex-column">
                    <span>{{$name}}</span>
                    <small>{{$admin_level_name}}</small>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-sm shadow-sm gap-3">
                  <a class="dropdown-item d-flex align-items-center py-2" href="profile.html"><i
                      class="icon-gitlab fs-4 me-3"></i>User Profile</a>
                  <a class="dropdown-item d-flex align-items-center py-2" href="{{url('/admin/logout')}}"><i
                      class="icon-log-out fs-4 me-3"></i>Logout</a>
                </div>
              </div>
            </div>
            <!-- App header actions end -->

          </div>
          <!-- App header ends -->

          <!-- App body starts -->
          <div class="app-body">

            <!-- Container starts -->
            <div class="container-fluid">

              <!-- Row start -->
              <div class="row">
                <div class="col-12 col-xl-12">
                  <h2 class="mb-2">@yield('header')</h2>
                  <h6 class="mb-4 fw-light">
                  @yield('sub_header')
                  </h6>
                </div>
              </div>
              <!-- Row end -->

              @yield('content')

            </div>
            <!-- Container ends -->

          </div>
          <!-- App body ends -->

          <!-- App footer start -->
          <div class="app-footer">
            <span>© Ymatrix Co. Ltd.</span>
          </div>
          <!-- App footer end -->

        </div>
        <!-- App container ends -->

      </div>
      <!-- Main container end -->

    </div>
    <!-- Page wrapper end -->

    <!-- *************
			************ JavaScript Files *************
		************* -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/validator.min.js') }}"></script>
    
    <!-- Custom JS files -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/vue.global.prod.js') }}"></script>
    @yield('script')
  </body>

</html>