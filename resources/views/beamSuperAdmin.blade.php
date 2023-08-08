@extends('shopify-app::layouts.default')
@extends('layouts.style')


@section('content')


<!-- You are: (shop domain name) -->
@extends('layouts.navigation')

<?php

// echo $injStatus;
$store=Auth::user();

?>


<div class="container">
    <div class="main">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-xs-10 col-sm-10 col-lg-10">
                        <p>Welcome at Beam Checkout</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-10 table-responsive">
                        <table id="myTable" class="table table-bordered user_datatable" style="left:-150px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Store Name</th>
                                    <th>Theme</th>
                                    <th>Product Page</th>
                                    <th>Cart Page</th>
                                    <th>Merchant ID</th>
                                    <th>API Key</th>
                                    <th>Mode</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($optionData) --}}
                                <?php
                                    $sr=1;
                                    // dd($optionData);
                                    foreach($optionData as $optdata)
                                    {
                                        echo "
                                            <tr>
                                                <td>". $sr ."</td>
                                                <td>". $optdata['id'] ."</td>
                                                <td>". $optdata['shop'] ."</td>
                                                <td>". $optdata['acttheme'] ."</td>
                                                <td>". $optdata['pgprod'] ."</td>
                                                <td>". $optdata['pgcart'] ."</td>
                                                <td>". $optdata['beammid'] ."</td>
                                                <td>". $optdata['beamapi'] ."</td>
                                                <td>". $optdata['beammode'] ."</td>
                                                <td><a href='javascript:void(0)' class='btn btn-primary btn-sm'>View</a></td>
                                            </tr>";
                                        $sr++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable();
        } );
    </script>
@endsection

@extends('layouts.script')


