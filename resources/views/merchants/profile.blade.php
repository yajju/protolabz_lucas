@extends('merchants.layout.appframe')
@section('profile','active')            
            @section('main_body')
                <div class="container-fluid">

                    <div class="page-wrapper">
                        <div class="page-breadcrumb">
                            <div class="row">
                                <div class="col-12 d-flex no-block align-items-center">
                                    <h4 class="page-title">Profile</h4>
                                    <div class="ms-auto text-end">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                                <li class="breadcrumb-item active" aria-current="page">
                                                Profile
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Column -->
                                <div class="row justify-content-center w-100">
                                    <div class="col-md-8 col-lg-6 col-xxl-3">
                                        <div class="card mt-0">
                                            <div class="card-body">
                                                @if(Session::has('errormessage'))
                                                    <div class="alert alert-danger" role="alert">{{ Session::get('errormessage') }}</div>
                                                @endif

                                                @if(session('succmsg'))
                                                    <div class="alert alert-success">
                                                        {!! session('succmsg') !!}
                                                    </div>
                                                @endif
                                                
                                                @if(session('errmsg'))
                                                    <div class="alert alert-danger">
                                                        {!! session('errmsg') !!}
                                                    </div>
                                                @endif
                                                <p class="text-center"><h4 class="text-center">Profile</h4></p>
                                                <br>
                                                <form role="form" class="text-start" action="{{url('merchant/profileupdate',[$id])}}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label for="exampleInputPassword1" class="form-label">Name</label>
                                                        <input type="text" class="form-control" id="exampleInputPassword1" name="cname" placeholder="Name">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="profile_image" class="form-label">Profile Image</label>
                                                        <img src="{{getAssetFilePath($profile_image)}}" alt="Profile Image" class="round-image" id="profileImageInput">
                                                        <input type="file" class="form-control" name="profile_image">
                                                        @error('profile_image')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- <div class="mb-4">
                                                        <label for="exampleInputPassword2" class="form-label">New Password</label>
                                                        <input type="password" class="form-control" id="exampleInputPassword2" name="newpassword" placeholder="New Password" required>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="exampleInputPassword3" class="form-label">Confirm Password</label>
                                                        <input type="password" class="form-control" id="exampleInputPassword3" name="confirmpassword" placeholder="Confirm Password" required>
                                                    </div> -->

                                                    <!-- <div class="d-flex align-items-center justify-content-between mb-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                                Remeber this Device
                                                            </label>
                                                        </div>
                                                        <a class="text-primary fw-bold" href="./index.html">Forgot Password ?</a>
                                                    </div> -->

                                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Update</button>
                                                    <!-- <div class="d-flex align-items-center justify-content-center">
                                                        <p class="fs-4 mb-0 fw-bold">New to Lucas?</p>
                                                        <a class="text-primary fw-bold ms-2" href="./authentication-register.html">Create an account</a>
                                                    </div> -->
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Column -->


                            </div>

                        </div>

                <div>
            @endsection

            @section('main_script')
            <script>
                // // JavaScript to update the profile image when a new image is uploaded
                // document.getElementById('profileImageInput').addEventListener('change', function (event) {
                //     const file = event.target.files[0];
                //     if (file) {
                //     const reader = new FileReader();
                //     reader.onload = function (e) {
                //         document.getElementById('profileImage').src = e.target.result;
                //     };
                //     reader.readAsDataURL(file);
                //     }
                // });
            </script>

            <script src="{{getAssetFilePath('adminassets/libs/jquery/dist/jquery.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/sidebarmenu.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/app.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/simplebar/dist/simplebar.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/dashboard.js')}}"></script>
            <script src="{{getAssetFilePath('assets/DataTables/datatables.min.js')}}"></script>
            <script>
                $("#merchantstab").DataTable({searching: false, paging: false, info: false});
            </script>
            @endsection
