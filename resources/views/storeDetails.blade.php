@extends('shopify-app::layouts.default')
@extends('layouts.style')
@section('content')
@extends('layouts.navigation')

<?php

        $buttonVisibility_pro_pg=$optionData['pgprod'];
        if($buttonVisibility_pro_pg=="no")
        {
            $defchecked_pro_pg="";
        }
        elseif($buttonVisibility_pro_pg=="yes")
        {
            $defchecked_pro_pg="checked";
        }
        else
        {
            $defchecked_pro_pg="";
        }

        $buttonVisibility_cart_pg=$optionData['pgcart'];
        if($buttonVisibility_cart_pg=="no")
        {
            $defchecked_cart_pg="";
        }
        elseif($buttonVisibility_cart_pg=="yes")
        {
            $defchecked_cart_pg="checked";
        }
        else
        {
            $defchecked_cart_pg="";
        }

        $buttonVisibility_chkout_pg=$optionData['pgchkout'];
        if($buttonVisibility_chkout_pg=="no")
        {
            $defchecked_chkout_pg="";
        }
        elseif($buttonVisibility_chkout_pg=="yes")
        {
            $defchecked_chkout_pg="checked";
        }
        else
        {
            $defchecked_chkout_pg="";
        }

        $buttonVisibility_UrlTest=$optionData['beammode'];
        if($buttonVisibility_UrlTest=="live")
        {
            $defchecked_urltest="";
        }
        else if($buttonVisibility_UrlTest=="test")
        {
            $defchecked_urltest="checked";
        }

?>

<div class="container">
    <div class="main">
        <div class="card">
            <div class="card-body">
                <div class="row wraps_first-div">
                    <div class="col-sm-12">
                        <div class="flex_wraps">
                            <p class="appear_wrap_text">This is how the button will appear on product, cart & checkout page. </p>
                            <button name='beamchkoutc' id='beamcheckoutbutton' onclick="alert('Hey, I am Working !');" class='btn-textc' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url("https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="wrap_line">
<div class="container">
    <div class="main">
        <div class="card">
            <div class="card-body">
                <div class="col-sm-6" style="padding:0px;">
                    <div class="">
                        <h3 class="w3-label wrap_text-heading">Beam   Credentials</h3>
                        <input type="hidden" value="{{ $buttonVisibility_UrlTest }}" id="buttonvisibile_test">
                        <div class="wrap_chkbox">
                            <input class="w3-check wrap_checkbox" type="checkbox" {{ $defchecked_urltest }} onchange="updateDbOption(getElementById('buttonvisibile_test').value,'beammode');"> <span class="wrap_checkobx_text">Enable Test Mode</span>
                        </div>
                        Merchant ID <input type="text" maxlength="250" value="{{ $optionData['beammid'] }}" id="beammerchentid" name="beammerchentid" class="w3-input w3-text-Indigo" style="font-weight: bold;">
                        API Key <input type="text" maxlength="250" value="{{ $optionData['beamapi'] }}" id="beamedapi" name="beamedapi" class="w3-input w3-text-Indigo" style="font-weight: bold;">
                        <br><button type="button" class="btn btn-primary" onclick="updateBeamCredentials(getElementById('beammerchentid').value,getElementById('beamedapi').value);" style="font-weight: bold;">Update</button>
                    </div>
                    {{-- <div class="w3-borderXX">
                        &nbsp; <br> <br> <br> <br> &nbsp;
                    </div> --}}
                    <div class="w3-borderXX">
                        &nbsp;
                    </div>
                </div>
                <div class="col-sm-6" style="padding:0px;">
                    <div class="w3-borderXX">
                        <h3 class="w3-label wrap_text-heading">Select Pages (Where you want to have beam checkout)</h3>
                        <input type="hidden" value="{{ $buttonVisibility_pro_pg }}" id="buttonvisibile_pro_pg">
                        <input type="hidden" value="{{ $buttonVisibility_cart_pg}}" id="buttonvisibile_cart_pg">
                        <input type="hidden" value="{{ $buttonVisibility_chkout_pg}}" id="buttonvisibile_chkout_pg">
                        <div class="wrap_chkbox">
                            <input class="w3-check wrap_checkbox" type="checkbox" {{ $defchecked_pro_pg }} onchange="updateDbOption(getElementById('buttonvisibile_pro_pg').value,'pgprod');"> <span class="wrap_checkobx_text">Product page</span>
                        </div>
                        <div class="wrap_chkbox">
                            <input class="w3-check wrap_checkbox" type="checkbox" {{ $defchecked_cart_pg }} onchange="updateDbOption(getElementById('buttonvisibile_cart_pg').value,'pgcart');"> <span class="wrap_checkobx_text">Cart page</span>
                        </div>
                        <div class="wrap_chkbox">
                            <input class="w3-check wrap_checkbox" type="checkbox" {{ $defchecked_chkout_pg }} onchange="updateDbOption(getElementById('buttonvisibile_chkout_pg').value,'pgchkout');"> <span class="wrap_checkobx_text">Widget page</span>
                        </div>
                    </div>
                    <div class="w3-borderXX">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        function updateBeamCredentials(beamid,beamapi)
        {
            $.ajax({
                url: 'UpdateBeamCr',
                type: 'get',
                data: {beamid: beamid,beamapi: beamapi},
                success: function(response)
                {
                    toastr[response.status](response.message);
                },
            });
        }
        function updateDbOption(newValue,targtObj)
        {
            if(newValue != '')
            {
                $.ajax({
                    url: 'UpdateDB',
                    type: 'get',
                    data: {newval: newValue,tarobj: targtObj},
                    success: function(response)
                    {
                        toastr[response.status](response.message);
                    },
                });
            }
            else
            {
                alert('Something Went Wrong');
            }

            if(targtObj=="pgprod")
            {
                if(newValue=="yes")
                {
                    newValue="no";
                    $defchecked_pro_pg="checked";
                }
                else
                {
                    newValue="yes";
                    $defchecked_pro_pg="";
                }
                $('#buttonvisibile_pro_pg').attr('value', newValue);
            }
            else if(targtObj=="pgcart")
            {
                if(newValue=="yes")
                {
                    newValue="no";
                    $defchecked_cart_pg="checked";
                }
                else
                {
                    newValue="yes";
                    $defchecked_cart_pg="";
                }
                $('#buttonvisibile_cart_pg').attr('value', newValue);
            }
            else if(targtObj=="pgchkout")
            {
                if(newValue=="yes")
                {
                    newValue="no";
                    $defchecked_chkout_pg="checked";
                }
                else
                {
                    newValue="yes";
                    $defchecked_chkout_pg="";
                }
                $('#buttonvisibile_chkout_pg').attr('value', newValue);
            }
            else if(targtObj=="beammode")
            {
                if(newValue=="live")
                {
                    newValue="test";
                    $defchecked_urltest="checked";
                }
                else if(newValue=="test")
                {
                    newValue="live";
                    $defchecked_urltest="";
                }
                $('#buttonvisibile_test').attr('value', newValue);
            }
        }
        function installbutton()
        {
            $.ajax({
                url: '/snippetInstall',
                type: 'get',
                data: {},
                success: function(response)
                {
                    for(var msg in response.messages)
                    {
                        var status = response.messages[msg]['status'];
                        var message = response.messages[msg]['message'];
                        toastr[status](message);
                    }
                },
            });
        }
    </script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@endsection


@extends('layouts.script')
