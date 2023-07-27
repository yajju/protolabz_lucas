@extends('User.layout.app')

<body id="page-top">

    @section('content')
        <!-- Page Wrapper -->
        <div id="wrapper">

            <!-- Sidebar -->
            <?php $cp = 'popup'; ?>
            @include('User.includes.sidebar')
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Topbar -->
                    @include('User.includes.header')
                    <!-- End of Topbar -->
<style>
    .gap-bx {
    gap: 33px;
    }
    </style>
                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <!-- popup page is appear  -->
                        <div class="h3-text   border-2 ">
                            <h2 class="text-dark font-weight-bold border-color sm-head1 mb-3">{{ __('message.Video Categories') }}</h2>
                          </div>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <!-- <h5 class="modal-title text-dark text-center " id="exampleModalLabel">SALES PAGE WITH THE
                                TWO OFFERED PACKAGES
                            </h5> -->
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="card">

                                            <div class="card-body text-center px-0 ">
                                                <h5 class="card-title font-weight-bold">{{ __('message.Access Denied Do you Want to Upgrade?') }}</h5>

                                               
                                                <a href="{{ url('User/twopackages') }}" class="btn btn-primary">{{ __('message.click here') }}</a>
                                            </div>
                                        </div>



                                    </div>
                                    <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save
                                changes</button>
                        </div> -->
                                </div>
                            </div>
                        </div>


                        <!--  -->
                        <!-- Page Heading -->
                       
                        <div class="row d-flex  align-items-center  mb-0 right mx-auto gap-bx container_popup">
                            {{-- <div class="col-12 col-sm-12 col-md-6 col-lg-2 pb-2 p-0 anc text-center ">
                                <!-- Button trigger modal -->
                                <h6 class="text-dark bg-white py-4  ">
                                    </h5>

                                    <button type="button" class=" bg-warning text-dark border-0 rounded "
                                        data-toggle="modal" data-target="#exampleModal">
                                        Click here
                                    </button>


                            </div> --}}
                         
                            {{-- <input type="hidden" value="$categories"> --}}
                           
                            @foreach($categories as $value)
                            <div class="col col-sm-12 col-md-6 col-lg-2  p-2 anc">
                                <a href="{{url('/User/video_sub/'.$value->id)}}" class="video_sub" data-catid="{{$value->id}}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/print1.png') }}" class="img-fluid w-100">
                                    </div>

                                    <h6 style="font-size: 12px;" class="bg-dark font-weight-bold text-white boxwidth text-center py-2 h6size ">
                                          {{ $value->category }}</h6>
                                </a>
                            </div>
                            @endforeach
                            {{-- <div class="col-12 col-sm-12 col-md-6 col-lg-2 mb-2 p-0 anc">
                                <a href="{{ url('User/digisign') }}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/print1.png') }}" class="img-fluid w-100 h-100">
                                    </div>

                                    <h5 class="bg-dark font-weight-bold text-white boxwidth text-center py-2 mb-0 ">
                                        DigiSigns4You</h5>
                                </a>
                            </div> --}}
                            {{-- <div class="col-12 col-sm-12 col-md-6 col-lg-2 mb-2 p-0 anc ">
                                <a href="{{ url('User/social-media') }}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/Social Media.png') }}" class="img-fluid w-100 h-100">
                                    </div>

                                    <h5 class="bg-dark font-weight-bold text-white boxwidth text-center py-2 mb-0 ">
                                          {{ GoogleTranslate::trans('Social-media', app()->getLocale()) }}</h5>
                                </a>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-2 mb-2 p-0 anc ">
                                <a href="{{ url('User/signage-text-detail') }}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/Signpage.png') }}" class="img-fluid w-100 h-100">
                                    </div>

                                    <h5 class="bg-dark font-weight-bold text-white boxwidth text-center py-2 mb-0 ">
                                         {{ GoogleTranslate::trans('Signpage', app()->getLocale()) }}</h5>
                                </a>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-2 mb-2 p-0 anc ">
                                <a href="{{ url('User/video') }}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/Signpage.png') }}" class="img-fluid w-100 h-100">
                                    </div>
                        
                                    <h5 class="bg-dark font-weight-bold text-white boxwidth text-center py-2 mb-0 ">
                                          {{ GoogleTranslate::trans('Video', app()->getLocale()) }}</h5>
                                </a>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-2 mb-2 p-0 anc">
                                <a href="{{ url('User/saveddesign-text-detail') }}">
                                    <div class="img-bx">
                                        <img src="{{ asset('public/IMG/my designs.png') }}" class="img-fluid w-100 h-100">
                                    </div>
                        
                                    <h5 class="bg-dark font-weight-bold text-white boxwidth text-center py-2 mb-0 ">
                                          {{ GoogleTranslate::trans('My design', app()->getLocale()) }}</h5>
                                </a>
                            </div> --}}
                        </div>

  <!-- Page Heading -->
  <div class="row d-flex  align-items-center  mb-0 right mx-auto gap-bx">

  
    

</div>

                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                @include('User.includes.footer')
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('message.ready to Leave') }}</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">{{ __('message.Select Logout below if you are ready to end your current session.') }}</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ __('message.cancel') }}</button>
                        <a class="btn btn-primary" href="{{url('User/logout')}}">{{ __('message.logout') }}</a>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap core JavaScript-->
        <script src="{{ asset('public/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('public/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{ asset('public/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{ asset('public/js/sb-admin-2.min.js') }}"></script>
    @endsection
</body>

</html>
