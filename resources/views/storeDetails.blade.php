@extends('shopify-app::layouts.default')
@extends('layouts.style')
@section('content')
@extends('layouts.navigation')

<?php


?>

<div class="container">
    <div class="main">
        <div class="card">
            <div class="card-body">
                <div class="row wraps_first-div">
                    <div class="col-sm-12">
                        <div class="flex_wraps">
                            Hello
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
                    <div class="col-sm-12" style="padding:0px;">
                        <div class="w3-borderXX">
                            <h3 class="w3-label wrap_text-heading">Select Pages (Where you want to have beam checkout)</h3>
                            Hi
                        </div>
                        <div class="w3-borderXX">
                            &nbsp;
                        </div>
                    </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
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
