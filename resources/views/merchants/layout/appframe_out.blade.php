<?php
// $profile_image=Auth::guard('merchant')->user()->profile_image;
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Doonpay - Merchant</title>
        <link rel="shortcut icon" type="image/png" href="{{getAssetFilePath('images/logo.png')}}" />
        <link rel="stylesheet" href="{{getAssetFilePath('adminassets/css/styles.min.css')}}" />
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

            <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
                <div class="d-flex align-items-center justify-content-center w-100">
                    <div class="row justify-content-center w-100">

                        @yield('main_body')

                        <div class="table-bx">
                            <h5> Doonpay &copy;
                                <script>document.write(new Date().getFullYear())</script>
                                <span> Powered by: Protolabz eServices</span>
                            </h5>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- ./Body Wrapper -->


        @yield('main_script')

    </body>
</html>
