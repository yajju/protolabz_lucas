@extends('merchants.layout.appframe_out')
<!-- @section('reports','active')             -->
            @section('main_body')

                        <div class="col-md-8 col-lg-6 col-xxl-3">
                            <div class="card mb-0">
                                <div class="card-body">
                                    @if(Session::has('errormessage'))
                                        <div class="alert alert-danger" role="alert">{{ Session::get('errormessage') }}</div>
                                    @endif
                                    <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                        <img src="{{getAssetFilePath('images/logo.png')}}" width="180" alt="">
                                    </a>
                                    <p class="text-center">Sign-In to your Merchant Panel</p>
                                    <form role="form" class="text-start" action="{{url('merchant/login')}}" method="post">
		                                    @csrf
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Username</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email" placeholder="Email" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="exampleInputPassword1" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password" required>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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
                                        <div class="d-flex align-items-center justify-content-center">
                                            <p class="fs-4 mb-0 fw-bold">New to Doonpay?</p>
                                            <a class="text-primary fw-bold ms-2" href="{{url('merchant/registration')}}">Create an account</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

            @endsection

            @section('main_script')
            <script src="{{getAssetFilePath('adminassets/libs/jquery/dist/jquery.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
            @endsection

