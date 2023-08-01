@extends('admin.layout.appframe')
@section('merchants','active')            
            @section('main_body')
                <div class="container-fluid">

                    <div class="page-wrapper">
                        <div class="page-breadcrumb">
                            <div class="row">
                                <div class="col-12 d-flex no-block align-items-center">
                                    <h4 class="page-title">Merchants</h4>
                                    <div class="ms-auto text-end">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                                <li class="breadcrumb-item active" aria-current="page">
                                                    Library
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="merchantstab" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Store Name</th>
                                                        <th>Token</th>
                                                        <th>Email</th>
                                                        <th>Installed Date</th>
                                                        <th>Status</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($result as $var)
                                                    <tr>
                                                        <td>{{$var->name}}</td>
                                                        <td>{{$var->password}}</td>
                                                        <td>{{$var->email}}</td>
                                                        <td>{{$var->created_at}}</tdvar->
                                                            <?php if ($var->deleted_at == '') {
                                                            $status = 'Active';
                                                        } else {
                                                            $status = 'Deactive';
                                                        }
                                                        ?>
                                                        <td>{{$status}}</td>

                                                    </tr>
                                                    @endforeach

                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Column -->


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
            <script src="{{getAssetFilePath('assets/DataTables/datatables.min.js')}}"></script>
            <script>
                $("#merchantstab").DataTable({searching: false, paging: false, info: false});
            </script>
            @endsection
