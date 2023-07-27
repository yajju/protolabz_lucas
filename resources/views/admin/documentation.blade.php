@extends('admin.layout.appframe')
@section('documentation','active')            
            @section('main_body')
                <div class="container-fluid">

                    <p>Documentation and Guides</p>

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
