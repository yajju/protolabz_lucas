@extends('shopify-app::layouts.default')
@extends('layouts.style')



@section('content')


<!-- You are: (shop domain name) -->
@extends('layouts.navigation')

<?php

// echo $injStatus;
$store=Auth::user();

?>


<div class="w3-container">
    <div class="main">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12">
                        <p>Welcome at Store {{ ucwords(str_replace(".myshopify.com","",$store->name)); }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent

    {{-- <script>
        actions.TitleBar.create(app, { title: 'Welcome ' });
    </script> --}}

    {{-- <script type="text/javascript">
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var TitleBar = actions.TitleBar;
        var Button = actions.Button;
        var Redirect = actions.Redirect;
        var titleBarOptions = {
            title: 'Welcome',
        };
        var myTitleBar = TitleBar.create(app, titleBarOptions);
    </script> --}}
@endsection

@extends('layouts.script')
