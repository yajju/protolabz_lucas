@extends('admin.layout.appframe')
@section('dashboard','active')            
            @section('main_body')
                <div class="container-fluid">


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
                                            <p class="text-center"><h4 class="text-center">Change Password of your Admin Panel</h4></p>
                                            <br>
                                            <form role="form" class="text-start" action="{{url('admin/change-password')}}" method="post">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="exampleInputPassword1" class="form-label">Current Password</label>
                                                    <input type="password" class="form-control" id="exampleInputPassword1" name="currentpassword" placeholder="Current Password">
                                                </div>

                                                <div class="mb-4">
                                                    <label for="exampleInputPassword2" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="exampleInputPassword2" name="newpassword" placeholder="New Password" required>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="exampleInputPassword3" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" id="exampleInputPassword3" name="confirmpassword" placeholder="Confirm Password" required>
                                                </div>

                                                <!-- <div class="d-flex align-items-center justify-content-between mb-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                                                        <label class="form-check-label text-dark" for="flexCheckChecked">
                                                            Remeber this Device
                                                        </label>
                                                    </div>
                                                    <a class="text-primary fw-bold" href="./index.html">Forgot Password ?</a>
                                                </div> -->

                                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</button>
                                                <!-- <div class="d-flex align-items-center justify-content-center">
                                                    <p class="fs-4 mb-0 fw-bold">New to Lucas?</p>
                                                    <a class="text-primary fw-bold ms-2" href="./authentication-register.html">Create an account</a>
                                                </div> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                <div>
            @endsection

            @section('main_script')
            <script src="{{asset('adminassets/libs/jquery/dist/jquery.min.js')}}"></script>
            <script src="{{asset('adminassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{asset('adminassets/js/sidebarmenu.js')}}"></script>
            <script src="{{asset('adminassets/js/app.min.js')}}"></script>
            <script src="{{asset('adminassets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
            <script src="{{asset('adminassets/libs/simplebar/dist/simplebar.js')}}"></script>
            <script src="{{asset('adminassets/js/dashboard.js')}}"></script>
            @endsection
