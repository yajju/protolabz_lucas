<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lucas - Admin</title>
        <link rel="shortcut icon" type="image/png" href="{{asset('adminassets/images/logos/favicon.png')}}" />
        <link rel="stylesheet" href="{{asset('adminassets/css/styles.min.css')}}" />
    </head>

    <body>
        <!--  Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

        
            <!-- Sidebar Start -->
            <aside class="left-sidebar">
                <!-- Sidebar scroll-->
                <div>
                    <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="#" class="text-nowrap logo-img">
                        <img src="{{asset('adminassets/images/logos/dark-logo.svg')}}" width="180" alt="" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                    </div>
                    <!-- Sidebar navigation-->
                    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('dashboard')" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                        </li>
                        <!-- <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">UI COMPONENTS</span>
                        </li> -->
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">Manage Merchants</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-alert-circle"></i>
                            </span>
                            <span class="hide-menu">Transaction History</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Analytics and Reports</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-mood-happy"></i>
                            </span>
                            <span class="hide-menu">Customer Support</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                            <i class="ti ti-typography"></i>
                            </span>
                            <span class="hide-menu">Documentation and Guides</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="authentication-login.html" aria-expanded="false">
                            <span>
                            <i class="ti ti-login"></i>
                            </span>
                            <span class="hide-menu">Login</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="authentication-register.html" aria-expanded="false">
                            <span>
                            <i class="ti ti-user-plus"></i>
                            </span>
                            <span class="hide-menu">Register</span>
                        </a>
                        </li>
                    </ul>
                    <div class="unlimited-access hide-menu bg-light-primary position-relative mb-7 mt-5 rounded">
                        <div class="d-flex">
                        <div class="unlimited-access-title me-3">
                            <h6 class="fw-semibold fs-4 mb-6 text-dark w-85">Upgrade to pro</h6>
                            <a href="#" target="_blank" class="btn btn-primary fs-2 fw-semibold lh-sm">Buy Pro</a>
                        </div>
                        <div class="unlimited-access-img">
                            <img src="{{asset('adminassets/images/backgrounds/rocket.png')}}" alt="" class="img-fluid">
                        </div>
                        </div>
                    </div>
                    </nav>
                    <!-- End Sidebar navigation -->
                </div>
                <!-- End Sidebar scroll-->
            </aside>
            <!--  Sidebar End -->

            @yield('main_body')
            
                        <div class="py-6 px-6 text-center">
                            <p class="mb-0 fs-4">
                                <a class="text-dark" target="_blank" href="https://protolabzit.com/">
                                        Powered by: Protolabz eServices
                                </a>
                            </p>
                        </div>
                    </div>
              </div>

        <!-- ./Body Wrapper -->
        </div>

        @yield('main_script')

    </body>
</html>
