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
                                                        <th>ID</th>
                                                        <th>Store Name</th>
                                                        <th>Telephone</th>
                                                        <th>Email</th>
                                                        <th>Registeration Date</th>
                                                        <th>Status</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($result as $var)
                                                    <tr>
                                                        <td>{{$var->id}}</td>
                                                        <td>{{$var->name}}</td>
                                                        <td>{{$var->telephone}}</td>
                                                        <td>{{$var->email}}</td>
                                                        <td>@if (isset($var->created_at))
                                                                {{ \Carbon\Carbon::parse($var->created_at)->format('Y-m-d') }}
                                                            @else
                                                            @endif
                                                        </td>
                                                            <?php
                                                                if (($var->status == '') || ($var->status == '0'))
                                                                {
                                                                    $status = '<a class="btn btn-danger m-1" href="#" onclick="updateStatus({{ $user->id }})">Not Approved</a>';
                                                                }
                                                                else if ($var->status == '1')
                                                                {
                                                                    $status = '<a class="btn btn-success m-1" href="#" onclick="updateStatus({{ $user->id }})">Approved</a>';
                                                                }
                                                            ?>
                                                        <!-- <td>{!! $status !!}</td> -->
                                                        <td>
                                                            @if (($var->status == '') || ($var->status == '0'))
                                                                <button class="btn btn-danger m-1" onclick="updateStatus({{ $var->id }},{{ $var->status }})" data-user-id="{{ $var->id }}" data-user-status="{{ $var->status }}">Deactivate</button>
                                                            @elseif ($var->status == '1')
                                                                <button class="btn btn-success m-1" onclick="updateStatus({{ $var->id }},{{ $var->status }})" data-user-id="{{ $var->id }}" data-user-status="{{ $var->status }}">Activate</button>
                                                            @endif
                                                        </td>

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
            <script>
                function updateStatus(userId,userStatusOLD) {
                    // Fetch the user ID from the button element
                    var button = $('button[data-user-id="' + userId + '"]');
                    var userStatus = button.attr('data-user-status');
                    // alert('userStatus : '+userStatus);

                    // Send an AJAX POST request to the route with the user ID
                    $.ajax({
                        url: '{{ url('admin/update-status') }}' + '/' + userId,
                        method: 'POST',
                        data: {
                            // _token: '{{ csrf_token() }}',
                            status: userStatus
                        },
                        success: function(response) {
                            console.log('Status updated successfully!');
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2200
                            })

                            var newStatus;
                            var newClass;
                            if(response.status == "1")
                            {
                                newStatus='Activate';
                                newClass='btn-success';
                            }
                            else if(response.status == "0")
                            {
                                newStatus='Deactivate';
                                newClass='btn-danger';
                            }
                            var button = $('button[data-user-id="' + userId + '"]');
                            button.text(newStatus).removeClass('btn-danger btn-success').addClass(newClass);
                            button.attr('data-user-status', response.status);

                        },
                        error: function(xhr, status, error) {
                            console.log('error');
                            Swal.fire({
                                position: 'top-center',
                                icon: 'error',
                                title: 'Some Error Found',
                                showConfirmButton: false,
                                timer: 1800
                            })
                        }
                    });
                }
            </script>
            @endsection
