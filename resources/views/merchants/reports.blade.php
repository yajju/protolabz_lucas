@extends('merchants.layout.appframe')
@section('reports','active')            
            @section('main_body')
                <div class="container-fluid">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Analytics and Reports</h5>
                            <p class="mb-0">This is a Analytics and Reports </p>
                        </div>
                    </div>

                <div>
            @endsection

            @section('main_script')
            <script src="{{getAssetFilePath('adminassets/libs/jquery/dist/jquery.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/sidebarmenu.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/app.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/libs/simplebar/dist/simplebar.js')}}"></script>
            <script src="{{getAssetFilePath('adminassets/js/dashboard.js')}}"></script>
            @endsection
