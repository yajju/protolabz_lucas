<?php
$profile_image=Auth::guard('merchant')->user()->profile_image;
if(!isset($profile_image)){$profile_image=getAssetFilePath('images/doonpay1200x1200.jpeg');}
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Doonpay - Merchant</title>
        <link rel="shortcut icon" type="image/png" href="{{getAssetFilePath('images/logo.png')}}" />
        <link rel="stylesheet" href="{{getAssetFilePath('adminassets/css/styles.min.css')}}" />

        <link href="{{getAssetFilePath('assets/DataTables/datatables.min.css')}}" rel="stylesheet">
        <style>
          .table-bx span {
              float: right;
          }

          .table-bx h5 {
              font-size: 14px;
              margin-bottom: 0px;
          }
          .round-image {
              border-radius: 50%;
              width: 150px; /* Set the desired width of the circular image */
              height: 150px; /* Set the desired height of the circular image */
              object-fit: cover; /* Ensure the image scales properly within the circular box */
              border: 2px solid #ccc; /* Add a border to the circular box */
          }
        </style>
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
                        <img src="{{getAssetFilePath('images/logo.png')}}" width="180" alt="" />
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
                        <!-- <span class="hide-menu">Home</span> -->
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('dashboard')" href="{{url('merchant/dashboard')}}" aria-expanded="false">
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
                        <a class="sidebar-link @yield('profile')" href="{{url('merchant/profile')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">My Profile</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('changepassword')" href="{{url('merchant/change-password')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Change Password</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('transactions')" href="{{url('merchant/transactions')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-alert-circle"></i>
                            </span>
                            <span class="hide-menu">Transaction History</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('reports')" href="{{url('merchant/reports')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Analytics and Reports</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('support')" href="{{url('merchant/support')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-mood-happy"></i>
                            </span>
                            <span class="hide-menu">Customer Support</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link @yield('documentation')" href="{{url('merchant/documentation')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-typography"></i>
                            </span>
                            <span class="hide-menu">Documentation and Guides</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="{{url('merchant/logout')}}" aria-expanded="false">
                            <span>
                            <i class="ti ti-logout"></i>
                            </span>
                            <span class="hide-menu">Logout</span>
                        </a>
                        </li>
                    </ul>
                    <!-- <div class="unlimited-access hide-menu bg-light-primary position-relative mb-7 mt-5 rounded">
                        <div class="d-flex">
                            <div class="unlimited-access-title me-3">
                                <h6 class="fw-semibold fs-4 mb-6 text-dark w-85">Upgrade to pro</h6>
                                <a href="#" target="_blank" class="btn btn-primary fs-2 fw-semibold lh-sm">Buy Pro</a>
                            </div>
                            <div class="unlimited-access-img">
                                <img src="{{getAssetFilePath('adminassets/images/backgrounds/rocket.png')}}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div> -->
                    </nav>
                    <!-- End Sidebar navigation -->
                </div>
                <!-- End Sidebar scroll-->
            </aside>
            <!--  Sidebar End -->


              <div class="body-wrapper">

                <!--  Header Start -->
                <header class="app-header">
                  <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                      <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                          <i class="ti ti-menu-2"></i>
                        </a>
                      </li>

                      <!-- <li class="nav-item">
                        <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                          <i class="ti ti-bell-ringing"></i>
                          <div class="notification bg-primary rounded-circle"></div>
                        </a>
                      </li> -->

                      <li class="nav-item dropdown">
                          <!-- <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="ti ti-bell-ringing"></i>
                            <div class="notification bg-primary rounded-circle"></div>
                          </a> -->
                          
                          <!-- <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="{{getAssetFilePath($profile_image)}}" alt="" width="35" height="35" class="rounded-circle">
                          </a> -->
                          <div class="dropdown-menu dropdown-menu-endXXX dropdown-menu-animate-up" aria-labelledby="drop2">
                            <div class="message-body">
                              <a href="{{url('merchant/profile')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">My Profile</p>
                              </a>
                              <a href="{{url('merchant/change-password')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-mail fs-6"></i>
                                <p class="mb-0 fs-3">Change Password</p>
                              </a>
                              <!-- <a href="{{url('merchant/new-registration')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-list-check fs-6"></i>
                                <p class="mb-0 fs-3">My Task</p>
                              </a> -->
                              <a href="{{url('merchant/logout')}}" class="btn btn-outline-primary mx-3 mt-2 d-block"><i class="ti ti-logout"></i> Logout</a>
                            </div>
                          </div>
                        </li>
                        
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                        <a href="{{url('merchant/logout')}}" class="btn btn-primary"><i class="ti ti-logout"></i> Logout</a>
                        <li class="nav-item dropdown">
                          <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <!-- <img src="{{getAssetFilePath('adminassets/images/profile/user-1.jpg')}}" alt="" width="35" height="35" class="rounded-circle"> -->
                            <img src="{{getAssetFilePath($profile_image)}}" alt="" width="35" height="35" class="rounded-circle">
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                            <div class="message-body">
                              <a href="{{url('merchant/profile')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">My Profile</p>
                              </a>
                              <a href="{{url('merchant/change-password')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-mail fs-6"></i>
                                <p class="mb-0 fs-3">Change Password</p>
                              </a>
                              <!-- <a href="{{url('merchant/new-registration')}}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-list-check fs-6"></i>
                                <p class="mb-0 fs-3">My Task</p>
                              </a> -->
                              <a href="{{url('merchant/logout')}}" class="btn btn-outline-primary mx-3 mt-2 d-block"><i class="ti ti-logout"></i> Logout</a>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </nav>
                </header>
                <!--  Header End -->

            @yield('main_body')

            <div class="table-bx">
                <h5> Doonpay &copy;
                    <script>document.write(new Date().getFullYear())</script>
                    <span> Powered by: Protolabz eServices</span>
                </h5>
            </div>

        <!-- ./Body Wrapper -->
        </div>

        @yield('main_script')

    </body>
</html>
