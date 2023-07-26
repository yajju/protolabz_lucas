<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\Shopify\Sync\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Session;
use Illuminate\Support\Facades\Http;
use Console;
use Shopify;

class AppProxyController extends Controller
{
    public function proxycalled(Request $request)
    {
        $action=$request->act;
        if(!isset($action))
        {
            $beampurchaseids = Storage::get('sesd.txt');
            if($beampurchaseids=="complete")
            {
                return null;
            }
            if($beampurchaseids<>"complete")
            {
                $shop = Auth::user();
                $urlHit=env('BEAM_URL').$beampurchaseids."/detail";
                $uname=env('BEAM_USER_NAME');
                $pass=env('BEAM_PASS_KEY');
                $authKeyNew = base64_encode($uname . ":" . $pass);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => ''.$urlHit.'',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Basic '.$authKeyNew.''
                    ),
                    ));
                $responses = curl_exec($curl);
                curl_close($curl);
                $responses = json_decode($responses, true);
                $payStatus=$responses['state'];
                    // $mobile=$responses['customer']['contactNumber'];
                    // $baddress=$responses['customer']['billingAddress']['fullStreetAddress'];
                    // dd($responses);


                if($payStatus=="complete")
                {
                        $orderAmt="";

                    $cartOrderDataA=DB::table('beamchkoutorderdata')
                            ->select('variantid','itemqty','shoporderid')
                            ->where('beampurchaseid',$beampurchaseids)
                            ->where('stat','0')
                            ->get();
                    $orderItemsData=array();
                    foreach($cartOrderDataA as $cartOrderData)
                    {
                        $thisData=array("variant_id" => "$cartOrderData->variantid", "quantity" => $cartOrderData->itemqty);//.",";
                        array_push($orderItemsData,$thisData);
                        $orderAmt=$cartOrderData->shoporderid;
                    }
                    $orderdata= [
                        "order" => [
                            "line_items" =>
                                $orderItemsData
                            ],
                        ];

                        $notes="";
                        $fname=@$responses['customer']['firstName'];
                        $lname=@$responses['customer']['lastName'];
                        $email=@$responses['customer']['email'];
                        $mobile=@$responses['customer']['contactNumber'];
                        $baddress=@$responses['customer']['billingAddress']['fullStreetAddress'];
                        $bcity=@$responses['customer']['billingAddress']['city'];
                        $bcountry=@$responses['customer']['billingAddress']['country'];
                        $bzip=@$responses['customer']['billingAddress']['postCode'];
                        $order_status="paid";

                        $orderdata['order']['customer'] = array(
                                "first_name" => "".$fname."",
                                "last_name" => "".$lname."",
                                "email" => "".$email.""
                        );
                        // $orderdata['order']['fulfillment_status'] =null;
                        $orderdata['order']['billing_address'] = array(
                            "first_name" => "".$fname."",
                            "last_name" => "".$lname."",
                            "address1" => "".$baddress."",
                            "phone" => "".$mobile."",
                            "city" => "".$bcity."",
                            "province" => "".$bcity."",
                            "country" => "".$bcountry."",
                            "zip" => "".$bzip.""
                        );
                        $orderdata['order']['shipping_address'] = array(
                            "first_name" => "".$fname."",
                            "last_name" => "".$lname."",
                            "address1" => "".$baddress."",
                            "phone" => "".$mobile."",
                            "city" => "".$bcity."",
                            "province" => "".$bcity."",
                            "country" => "".$bcountry."",
                            "zip" => "".$bzip.""
                        );
                        $orderdata['order']['note'] = $notes;
                        $orderdata['order']['email'] = $email;
                        $orderdata['order']['transactions'] = array(
                            [
                                "kind" => "authorization",
                                "status" => "success",
                                "amount" => $orderAmt
                            ]
                        );
                        $orderdata['order']['financial_status'] = $order_status;
                        // Log::info("Address : ".$baddress);

                    // dd($orderdata);
                    // Log::info("B : ".$orderdata);
                    // $orderdata1 = json_decode($orderdata,true);
                    $orderdata1 = json_encode($orderdata);
                    // Log::info("B : ".$orderdata1);
                    $orderdata = json_decode(json_encode($orderdata),true);
                    // dd($orderdata);

                    $shopApi = $shop->api()->rest('POST', '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders.json',$orderdata)['body']['order'];
                    // dd($shopApi);
                    // $shopApi = $shop->api()->rest('POST', '/admin/api/2022-10/orders.json',$orderdata)['body']['order'];
                    $shopApi = json_decode(json_encode($shopApi), true);
                    $order_id=$shopApi['id'];
                    $order_stat_url=$shopApi['order_status_url'];
                    if($order_stat_url<>'')
                    {
                        $res1 = DB::table('beamchkoutorderdata')
                                        ->where('beampurchaseid',$beampurchaseids)
                                        ->update(['stat' => '1','shoporderid' => $order_id]);
                        /*$res2 = DB::table('beamcustomer')
                            ->where('beampurchaseid',$beampurchaseids)
                            ->update(['order_status' => $order_status, 'shoporderid' => $order_id]);*/
                    }
                    // Storage::put('sesd.txt', $payStatus);
                    return redirect($order_stat_url);
                }
                elseif($payStatus=="beamed")
                {
                    $beampurchaseids = Storage::get('sesd.txt');
                    $urlHit="https://stg-pay.beamcheckout.com/shopifytest/".$beampurchaseids;
                    return redirect($urlHit);
                }
            }
        }
        if($action=="btnshowornot")
        {
            $putOnPage=$request->whichbutton;
            $resValue='Show';
            $btnValue="";
            $modalDataBtn="";
            $shop = Auth::user();
            // $stylethis="'width:357px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'";
            $stylethis="

<style>

@media only screen and (max-width: 360px)  {
button#beamcheckoutbutton{
    width: 357px;
    height: 49px;
    border-style: none;
    border-radius: 10px;
    background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
    background-size: cover;
    cursor: pointer !important;
    margin: 0px auto;
    margin-top: 10px !important;
    text-align: center;
}
}
@media only screen and (max-width: 676px) {
    button#beamcheckoutbutton{
    width: 311px;
        height: 49px;
        border-style: none;
        border-radius: 10px;
        background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
        background-size: cover;
        cursor: pointer !important;
        margin: 0px auto;
        margin-top: 10px !important;
        text-align: center;
    }
    }
    button#beamcheckoutbutton{
    width: 439px;
    height: 49px;
    border-style: none;
    border-radius: 10px;
    background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
    background-size: cover;
    cursor: pointer !important;
    margin: 0px auto;
    margin-top: 10px !important;
    text-align: center;
}

</style>

                ";
            if($putOnPage=="product")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgprod')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgprod;
                if($dbShowOrNot=="yes")
                {
                    $btnValue="$stylethis <button name='beamchkout' id='beamcheckoutbutton' onclick=\"addItemNew(getElementById('selectedproduct').value,getElementById('selproductqty').value);return false;\" class='btn-text'></button>";
                    //<button name='beamchkout' id='beamcheckoutbutton' onclick="addItem(getElementById('selectedproduct').value,getElementById('selproductqty').value);return false;" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                    //1178<button onclick="addItem();return false;" style='width:11px;height:11px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                    // $modalDataBtn="<button name='beamchkout' id='beamcheckoutbutton' onclick=\"$('#getCodeModal').modal('show');\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                }
            }
            elseif($putOnPage=="cart")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgcart')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgcart;
                if($dbShowOrNot=="yes")
                {
                    $btnValue="$stylethis <button name='beamchkoutc' id='beamcheckoutbutton' onclick=\"beambuttoncr();return false;\" class='btn-textc'></button>";
                    // $modalDataBtn="<button name='beamchkout' id='beamcheckoutbutton' onclick=\"$('#getCodeModal').modal('show');\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                }
            }
            return response()->json(array('res'=>$resValue,'btn'=>$btnValue));
        }
        /*if($action=="getcustinfo")
        {
            // return view("custinfoform");
            // $modalData=view("custinfoform");
            //<button onclick="$('#getCodeModal').modal({backdrop: 'static', keyboard: false},'show');" style='width:11px;height:11px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
            //<button onclick="$('#getCodeModal').modal('show');" style='width:11px;height:11px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                            //     <button type="btn button" style="top:-20px;align:center;" class="close" data-dismiss="modal" aria-label="Close">
                            //     <span aria-hidden="true">&times;</span>
                            // </button>
            // $putOnPage=$request->onpage;
            $putOnPage=$request->onpage;
            // $resValue='Show';
            // $btnValue="";
            $modalDataBtn="";
            $shop = Auth::user();
            if($putOnPage=="product")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgprod')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgprod;
                if($dbShowOrNot=="yes")
                {
                    // $btnValue="    <button name='beamchkout' id='beamcheckoutbutton' onclick=\"addItemNew(getElementById('selectedproduct').value,getElementById('selproductqty').value);return false;\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                    //<button name='beamchkout' id='beamcheckoutbutton' onclick="addItem(getElementById('selectedproduct').value,getElementById('selproductqty').value);return false;" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                    //1178<button onclick="addItem();return false;" style='width:11px;height:11px;border-style:none;border-radius:10px; background:url("https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>
                    $modalDataBtn="<button name='beamchkout' id='beamcheckoutbutton' onclick=\"$('#getCodeModal').modal('show');\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                }
            }
            elseif($putOnPage=="cart")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgcart')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgcart;
                if($dbShowOrNot=="yes")
                {
                    // $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick=\"beambuttoncr();return false;\" class='btn-textc' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                    $modalDataBtn="<button name='beamchkout' id='beamcheckoutbutton' onclick=\"$('#getCodeModal').modal('show');\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                }
            }
            // return response()->json(array('res'=>$resValue,'btn'=>$btnValue));

            $modalData=
                '
                <div class="modal fade in" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">


                            <div class="col-sm-12 md-form mb-5">
                                <hr>
                                <center>
                                    <h4>Customer Info <i class="glyphicon glyphicon-remove close" data-dismiss="modal"></i> </h4>
                                </center>
                            </div>
                            <div class="modal-body mx-3">

                                            <div class="col-sm-6 md-form mb-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                    <input type="text" class="form-control validate" name="fname" id="fname" placeholder="First Name" maxlength="100" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 md-form mb-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                    <input type="text" class="form-control validate" name="lname" id="lname" placeholder="Last Name" maxlength="100">
                                                </div>
                                            </div>

                                            <div class="col-sm-6 md-form mb-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                                    <input type="email" class="form-control validate" name="email" id="email" placeholder="Email" maxlength="100" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 md-form mb-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                                    <input type="integer" class="form-control validate" name="mobile" id="mobile" placeholder="Mobile" maxlength="15" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 md-form mb-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                                                    <input type="text" class="form-control validate" name="notes" id="notes" placeholder="Notes" maxlength="255">
                                                </div>
                                            </div>

                                <div class="col-sm-12 md-form mb-5">
                                    <hr>
                                    <center><h4>Billing Details</h4></center>
                                </div>

                                <div class="col-sm-6 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" class="form-control validate" name="bname" id="bname" placeholder="First Name" maxlength="100">
                                    </div>
                                </div>
                                <div class="col-sm-6 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" class="form-control validate" name="blname" id="blname" placeholder="Last Name" maxlength="100">
                                    </div>
                                </div>

                                <div class="col-sm-12 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                        <input type="text" class="form-control validate" name="baddress" id="baddress" placeholder="Address" maxlength="255">
                                    </div>
                                </div>

                                <div class="col-sm-6 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                        <input type="text" class="form-control validate" name="bmobile" id="bmobile" placeholder="Mobile" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-sm-6 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" class="form-control validate" name="bcity" id="bcity" placeholder="City" maxlength="150">
                                    </div>
                                </div>

                                <div class="col-sm-4 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
                                        <input type="text" class="form-control validate" name="bprovince" id="bprovince" placeholder="Province" maxlength="150">
                                    </div>
                                </div>
                                <div class="col-sm-4 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                        <input type="text" class="form-control validate" name="bcountry" id="bcountry" placeholder="Country" maxlength="150">
                                    </div>
                                </div>
                                <div class="col-sm-4 md-form mb-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-pushpin"></i></span>
                                        <input type="text" class="form-control validate" name="bzip" id="bzip" placeholder="Zip" maxlength="10">
                                    </div>
                                </div>

                                        <div class="col-sm-12 md-form mb-5">
                                            <hr>
                                            <center><h4>Delivery Details</h4></center>
                                        </div>

                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                <input type="text" class="form-control validate" name="dname" id="dname" placeholder="First Name" maxlength="100">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                <input type="text" class="form-control validate" name="dlname" id="dlname" placeholder="Last Name" maxlength="100">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                                <input type="text" class="form-control validate" name="daddress" id="daddress" placeholder="Address" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                                <input type="text" class="form-control validate" name="dmobile" id="dmobile" placeholder="Mobile" maxlength="15">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                                <input type="text" class="form-control validate" name="dcity" id="dcity" placeholder="City" maxlength="150">
                                            </div>
                                        </div>

                                        <div class="col-sm-4 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
                                                <input type="text" class="form-control validate" name="dprovince" id="dprovince" placeholder="Province" maxlength="150">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                                <input type="text" class="form-control validate" name="dcountry" id="dcountry" placeholder="Country" maxlength="150">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-pushpin"></i></span>
                                                <input type="text" class="form-control validate" name="dzip" id="dzip" placeholder="Zip" maxlength="10">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 md-form mb-5">
                                            <hr>
                                            <center>
                                                <button class="btn btn-indigo btn-info" onclick="getcustomerdetails(document.querySelectorAll(\'#fname, #lname, #email, #mobile, #notes, #bname, #blname, #baddress, #bmobile, #bcity, #bprovince, #bcountry, #bzip, #dname, #dlname, #daddress, #dmobile, #dcity, #dprovince, #dcountry, #dzip, #selectedproduct, #selproductqty\'),\''.$putOnPage.'\');return false;">Continue <i class="fa fa-thumbs-up ml-1"></i></button>
                                                <i class="glyphicon glyphicon-remove close" data-dismiss="modal"></i>
                                            </center>
                                        </div>

                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <div class="col-sm-12 md-form mb-5">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    ';
            */
            /*$modalData=
            '
            <div class="modal fade in" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">


                    <div class="col-sm-12 md-form mb-5">
                        <hr>
                        <center>
                            <h4>Customer Info <i class="glyphicon glyphicon-remove close" data-dismiss="modal"></i> </h4>
                        </center>
                    </div><form name="f1" method="post" class="was-validated">
                    <div class="modal-body mx-3">

                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                <input type="text" class="form-control validate" name="fname" id="fname" placeholder="First Name" maxlength="100" value="First Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                <input type="text" class="form-control validate" name="lname" id="lname" placeholder="Last Name" maxlength="100" value="Last Name" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                                <input type="email" class="form-control validate" name="email" id="email" placeholder="Email" maxlength="100" value="adasd" pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                                <input type="integer" class="form-control validate" name="mobile" id="mobile" placeholder="Mobile" maxlength="15" value="Mobile" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 md-form mb-5">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                                                <input type="text" class="form-control validate" name="notes" id="notes" placeholder="Notes" maxlength="255" value="Notes">
                                            </div>
                                        </div>

                        <div class="col-sm-12 md-form mb-5">
                            <hr>
                            <center><h4>Billing Details</h4></center>
                        </div>

                        <div class="col-sm-6 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input type="text" class="form-control validate" name="bname" id="bname" placeholder="First Name" maxlength="100" value="BFirst Name">
                            </div>
                        </div>
                        <div class="col-sm-6 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input type="text" class="form-control validate" name="blname" id="blname" placeholder="Last Name" maxlength="100" value="BLast Name">
                            </div>
                        </div>

                        <div class="col-sm-12 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                <input type="text" class="form-control validate" name="baddress" id="baddress" placeholder="Address" maxlength="255" value="BAddress">
                            </div>
                        </div>

                        <div class="col-sm-6 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                <input type="text" class="form-control validate" name="bmobile" id="bmobile" placeholder="Mobile" maxlength="15" value="BMobile">
                            </div>
                        </div>
                        <div class="col-sm-6 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                <input type="text" class="form-control validate" name="bcity" id="bcity" placeholder="City" maxlength="150" value="BCity">
                            </div>
                        </div>

                        <div class="col-sm-4 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
                                <input type="text" class="form-control validate" name="bprovince" id="bprovince" placeholder="Province" maxlength="150" value="BProvince">
                            </div>
                        </div>
                        <div class="col-sm-4 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                <input type="text" class="form-control validate" name="bcountry" id="bcountry" placeholder="Country" maxlength="150" value="BCountry">
                            </div>
                        </div>
                        <div class="col-sm-4 md-form mb-5">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-zip"></i></span>
                                <input type="text" class="form-control validate" name="bzip" id="bzip" placeholder="Zip" maxlength="10" value="BZip">
                            </div>
                        </div>

                                    <div class="col-sm-12 md-form mb-5">
                                        <hr>
                                        <center><h4>Delivery Details</h4></center>
                                    </div>

                                    <div class="col-sm-6 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                            <input type="text" class="form-control validate" name="dname" id="dname" placeholder="First Name" maxlength="100" value="DFirst Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                            <input type="text" class="form-control validate" name="dlname" id="dlname" placeholder="Last Name" maxlength="100" value="DLast Name">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                            <input type="text" class="form-control validate" name="daddress" id="daddress" placeholder="Address" maxlength="255" value="DAddress">
                                        </div>
                                    </div>

                                    <div class="col-sm-6 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                                            <input type="text" class="form-control validate" name="dmobile" id="dmobile" placeholder="Mobile" maxlength="15" value="DMobile">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                            <input type="text" class="form-control validate" name="dcity" id="dcity" placeholder="City" maxlength="150" value="DCity">
                                        </div>
                                    </div>

                                    <div class="col-sm-4 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-tag"></i></span>
                                            <input type="text" class="form-control validate" name="dprovince" id="dprovince" placeholder="Province" maxlength="150" value="DProvince">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                            <input type="text" class="form-control validate" name="dcountry" id="dcountry" placeholder="Country" maxlength="150" value="DCountry">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 md-form mb-5">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-zip"></i></span>
                                            <input type="text" class="form-control validate" name="dzip" id="dzip" placeholder="Zip" maxlength="10" value="DZip">
                                        </div>
                                    </div>

                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <center><br>
                            <button type="submit" class="validate btn btn-indigo btn-info" onclick="getcustomerdetails(document.querySelectorAll(\'#fname, #lname, #email, #mobile, #notes, #bname, #blname, #baddress, #bmobile, #bcity, #bprovince, #bcountry, #bzip, #dname, #dlname, #daddress, #dmobile, #dcity, #dprovince, #dcountry, #dzip, #selectedproduct, #selproductqty\'),\''.$putOnPage.'\');return false;">Send <i class="fa fa-paper-plane-o ml-1"></i></button>
                        </center><br><br></form>
                    </div>
                    </div>
                </div>
                </div>
                ';*///<button class="btn btn-indigo btn-info" onclick="addItem(getElementById(\'selectedproduct\').value,getElementById(\'selproductqty\').value);return false;">Send <i class="fa fa-paper-plane-o ml-1"></i></button>
                    //<button class="btn btn-indigo btn-info" onclick="getcustomerdetails(document.querySelectorAll(\'#selectedproduct, #selproductqty\'));addItemNew(getElementById(\'selectedproduct\').value,getElementById(\'selproductqty\').value);return false;">Send <i class="fa fa-paper-plane-o ml-1"></i></button>
                    //<button class="btn btn-indigo btn-info" onclick="getcustomerdetails(document.querySelectorAll(\'#selectedproduct, #selproductqty\'));return false;">Send <i class="fa fa-paper-plane-o ml-1"></i></button>
                    //$modalDataBtn="<button onclick=\"$('#getCodeModal').modal('show');\" style='width:11px;height:11px;border-style:none;border-radius:10px; background:url(\"https://www.pngplay.com/wp-content/uploads/8/Upload-Icon-Logo-PNG-Photos.png\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                    // $modalDataBtn="<button name='beamchkout' id='beamcheckoutbutton' onclick=\"$('#getCodeModal').modal('show');\" class='btn-text' style='width:347px;height:53px;border-style:none;border-radius:10px; background:url(\"https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg\") no-repeat; background-size: cover; cursor: pointer !important; margin: 0px auto; margin-top: 10px !important;'></button>";
                /*'
                <div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <p><strong>Lorem Ipsum is simply dummy</strong> text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting,
                            remaining essentially unchanged.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" ng-click="cancel()">Close</button>
                        <button type="button" class="btn btn-primary" ng-click="ok()">Save changes</button>
                    </div>
                    </div>
                    </div>
                </div> ';*/
            /*return response()->json(array('res'=>'ok','datashow'=>$modalData,'databtn'=>$modalDataBtn));
        }
        if($action=="createcustomer")
        {
            $shop = Auth::user();
            $proaray=$request->pro_itemgetdata;
            $itemNumber=sizeof($proaray);

            if($itemNumber==23)
            {
                $proid=$proaray[0];
                $proqty=$proaray[1];
            }

            $fname=$proaray[$itemNumber-21];
            $lname=$proaray[$itemNumber-20];
            $email=$proaray[$itemNumber-19];
            $mobile=$proaray[$itemNumber-18];
            $notes=$proaray[$itemNumber-17];
            $bname=$proaray[$itemNumber-16];
            $blname=$proaray[$itemNumber-15];
            $baddress=$proaray[$itemNumber-14];
            $bmobile=$proaray[$itemNumber-13];
            $bcity=$proaray[$itemNumber-12];
            $bprovince=$proaray[$itemNumber-11];
            $bcountry=$proaray[$itemNumber-10];
            $bzip=$proaray[$itemNumber-9];
            $dname=$proaray[$itemNumber-8];
            $dlname=$proaray[$itemNumber-7];
            $daddress=$proaray[$itemNumber-6];
            $dmobile=$proaray[$itemNumber-5];
            $dcity=$proaray[$itemNumber-4];
            $dprovince=$proaray[$itemNumber-3];
            $dcountry=$proaray[$itemNumber-2];
            $dzip=$proaray[$itemNumber-1];

            // Log::info("Size : ".$itemNumber);
            // Log::info("ID : ".$proid);
            // Log::info("Qty : ".$proqty);
            // sleep(3000);
            $insertData = [
                'shop' => $shop->name,
                'customer_email' => $email,
                'mobile' => $mobile,
                'order_status' => 'pending',
                'customer_note' => $notes,
                'cu_first_name' => $fname,
                'cu_last_name' => $lname,
                'bl_first_name' => $bname,
                'bl_last_name' => $blname,
                'bl_address' => $baddress,
                'bl_phone' => $bmobile,
                'bl_city' => $bcity,
                'bl_province' => $bprovince,
                'bl_country' => $bcountry,
                'bl_zip' => $bzip,
                'dl_first_name' => $dname,
                'dl_last_name' => $dlname,
                'dl_address' => $daddress,
                'dl_phone' => $dmobile,
                'dl_city' => $dcity,
                'dl_province' => $dprovince,
                'dl_country' => $dcountry,
                'dl_zip' => $dzip,
                'order_amt' => '0',
                'beampurchaseid' => '',
                'shoporderid' => '',
            ];
            $insertCustID = DB::table('beamcustomer')->insertGetId($insertData);
            DB::commit();

            return response()->json(array('stat'=>'success','custid'=>$insertCustID));
        }*/
        /*if($action=="createurl")
        {
            sleep(15);
            $paymentLink="https://www.linkedin.com";
            return response()->json(array('paymentLink'=>$paymentLink));
        }
        if($action=="createurlprdct")
        {
            sleep(30);
            $paymentLink="https://www.facebook.com";
            return response()->json(array('paymentLink'=>$paymentLink));
        }*/

        if($action=="createurl")
        {
            $urlHit=env('BEAM_URL');
            $uname=env('BEAM_USER_NAME');
            $pass=env('BEAM_PASS_KEY');
            $authKeyNew = base64_encode($uname . ":" . $pass);
            $shop = Auth::user();
            $ncustid='0';//$request->pro_custid;
            $ncart_token=$request->cart_token;
            $ncart_total_price=$request->cart_total_price;
            $ncart_total_price=$ncart_total_price/100;
            $ncart_items_number=$request->cart_items_number;
            $ncart_items_finalprice=$request->cart_items_finalprice;
            $ncart_items_images=$request->cart_items_images;
            $ncart_items_name=$request->cart_items_name;
            $ncart_items_descp=$request->cart_items_descp;
            $ncart_items_qty=$request->cart_items_qty;
            $ncart_items_variantid=$request->cart_items_variantid;
            $orderItemsData='';
            $lastiItem=$ncart_items_number-1;
            for($a=0;$a<$ncart_items_number;$a++)
            {
                $ncart_items_finalpricea=$ncart_items_finalprice[$a]/100;
                if($a==$lastiItem)
                {
                    $orderItemsData.='{
                        "product": {
                        "description": "'.$ncart_items_descp[$a].'",
                        "imageUrl": "'.$ncart_items_images[$a].'",
                        "name": "'.$ncart_items_name[$a].'",
                        "price": '.$ncart_items_finalpricea.',
                        "sku": "'.$ncart_items_variantid[$a].'"
                    },
                        "quantity": '.$ncart_items_qty[$a].'
                    }';
                }
                else
                {
                    $orderItemsData.='{
                        "product": {
                        "description": "'.$ncart_items_descp[$a].'",
                        "imageUrl": "'.$ncart_items_images[$a].'",
                        "name": "'.$ncart_items_name[$a].'",
                        "price": '.$ncart_items_finalpricea.',
                        "sku": "'.$ncart_items_variantid[$a].'"
                    },
                        "quantity": '.$ncart_items_qty[$a].'
                    },';
                }
                $insertData = [
                    'shop' => $shop->name,
                    'custid' => $ncustid,
                    'carttoken' => $ncart_token,
                    'variantid' => $ncart_items_variantid[$a],
                    'itemqty' => $ncart_items_qty[$a],
                    'beampurchaseid'=>'up',
                    'stat'=>'0',
                    'shoporderid'=>'0',
                ];
                $res = DB::table('beamchkoutorderdata')->insert($insertData);
                DB::commit();
            }
            $emptyField="";
            $currentTime = date("d-m-Y H:i:s");//,mktime(date("H")+5,date("i")+30));
            $expiryNew = date("Y-m-d H:i:s", strtotime("$currentTime +15 mins"));
            $expiryNew=str_replace(" ","T",$expiryNew);
            $expiryNew.="Z";
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => ''.$urlHit.'',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                    "channel": "shopify",
                    "expiry": "'.$expiryNew.'",
                    "order": {
                        "currencyCode": "THB",
                        "description": "Shopify Order",
                        "merchantReference": "'.$emptyField.'",
                        "merchantReferenceId": "'.$ncart_token.'",
                        "netAmount": '.$ncart_total_price.',
                        "orderItems": [
                            '.$orderItemsData.'
                        ],
                        "totalAmount": '.$ncart_total_price.',
                        "totalDiscount": 0
                    },
                    "redirectUrl": "https://'.$shop->name.'/apps/proxy",
                    "requiredFieldsFormId": "beamdatacompany-checkout",
                    "supportedPaymentMethods": [
                            "creditCard"
                    ]
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.$authKeyNew.''
            ),
            ));
            $responses = curl_exec($curl);
            curl_close($curl);
            $responses = json_decode($responses, true);
            $purchaseId=$responses['purchaseId'];
            $paymentLink=$responses['paymentLink'];
            Storage::put('sesd.txt', $purchaseId);
            $res1 = DB::table('beamchkoutorderdata')
                ->where('beampurchaseid', 'up')
                ->update(['beampurchaseid' => $purchaseId, 'shoporderid' => $ncart_total_price]);
            /*$res2 = DB::table('beamcustomer')
                ->where('id', $ncustid)
                ->update(['beampurchaseid' => $purchaseId, 'order_amt' => $ncart_total_price]);
            Log::info("AMOUNT \nbeampurchaseid : ". $purchaseId . '\norder_amt : ' . $ncart_total_price);*/
            return response()->json(array('paymentLink'=>$paymentLink));
        }
        if($action=="createurlprdct")
        {
            $urlHit=env('BEAM_URL');
            $uname=env('BEAM_USER_NAME');
            $pass=env('BEAM_PASS_KEY');
            $authKeyNew = base64_encode($uname . ":" . $pass);
            $shop = Auth::user();
            $ncustid='0';//$request->pro_custid;
            $proid=$request->pro_variantid;
            $proqty=$request->pro_qty;
            $products = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/variants/'.$proid.'.json');//,['limit'=>4]
            $products = $products['body']['variant'];
            $products = json_encode($products);
            $products = json_decode($products, true);
            $productID=$products['product_id'];
            $imageID=$products['image_id'];
            if($imageID!=null)
            {
                $imageName = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$productID.'/images/'.$imageID.'.json');//,['limit'=>4]
                $imageName = $imageName['body']['image'];
                $imageName = json_encode($imageName);
                $imageName = json_decode($imageName, true);
            }
            else
            {
                $imageName = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$productID.'.json');//,['limit'=>4]
                $imageName = $imageName['body']['product']['image'];
                $imageName = json_encode($imageName);
                $imageName = json_decode($imageName, true);
            }
            if(!isset($imageName['src']))
            {
                $imageName['src']="https://pbs.twimg.com/profile_images/1506629865985949699/vHbnimko_400x400.jpg";
            }
            $ncart_token=mt_rand(9999,99999999);
            $ncart_total_price=$products['price'];
            $ncart_total_price=$ncart_total_price*$proqty;
            $ncart_items_number=$proqty;
            $ncart_items_finalprice=$ncart_total_price;
            $ncart_items_images=$imageName['src'];
            $ncart_items_name=$products['title'];
            $ncart_items_descp=$products['title'];
            $ncart_items_qty=$proqty;
            $ncart_items_variantid=$proid;
            $ncart_items_finalpricea=$products['price'];
                $orderItemsData='{
                    "product": {
                    "description": "'.$ncart_items_descp.'",
                    "imageUrl": "'.$ncart_items_images.'",
                    "name": "'.$ncart_items_name.'",
                    "price": '.$ncart_items_finalpricea.',
                    "sku": "'.$ncart_items_variantid.'"
                },
                    "quantity": '.$ncart_items_qty.'
                }';
            $insertData = [
                'shop' => $shop->name,
                'custid' => $ncustid,
                'carttoken' => $ncart_token,
                'variantid' => $ncart_items_variantid,
                'itemqty' => $ncart_items_qty,
                'beampurchaseid'=>'up',
                'stat'=>'0',
                'shoporderid'=>'0',
            ];
            $res = DB::table('beamchkoutorderdata')->insert($insertData);
            DB::commit();
            $emptyField="";
            $currentTime = date("d-m-Y H:i:s");//,mktime(date("H")+5,date("i")+30));
            $expiryNew = date("Y-m-d H:i:s", strtotime("$currentTime +15 mins"));
            $expiryNew=str_replace(" ","T",$expiryNew);
            $expiryNew.="Z";
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => ''.$urlHit.'',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                    "channel": "shopify",
                    "expiry": "'.$expiryNew.'",
                    "order": {
                        "currencyCode": "THB",
                        "description": "Shopify Order",
                        "merchantReference": "'.$emptyField.'",
                        "merchantReferenceId": "'.$ncart_token.'",
                        "netAmount": '.$ncart_total_price.',
                        "orderItems": [
                            '.$orderItemsData.'
                        ],
                        "totalAmount": '.$ncart_total_price.',
                        "totalDiscount": 0
                    },
                    "redirectUrl": "https://'.$shop->name.'/apps/proxy",
                    "requiredFieldsFormId": "beamdatacompany-checkout",
                    "supportedPaymentMethods": [
                            "creditCard"
                    ]
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.$authKeyNew.''
            ),
            ));
            $responses = curl_exec($curl);
            curl_close($curl);
            $responses = json_decode($responses, true);
            $purchaseId=$responses['purchaseId'];
            $paymentLink=$responses['paymentLink'];
            Storage::put('sesd.txt', $purchaseId);
            $res1 = DB::table('beamchkoutorderdata')
                ->where('beampurchaseid', 'up')
                ->update(['beampurchaseid' => $purchaseId, 'shoporderid' => $ncart_total_price]);
            /*$res2 = DB::table('beamcustomer')
                ->where('id', $ncustid)
                ->update(['beampurchaseid' => $purchaseId, 'order_amt' => $ncart_total_price]);
            Log::info("AMOUNT \nbeampurchaseid : ". $purchaseId . '\norder_amt : ' . $ncart_total_price);*/

            return response()->json(array('paymentLink'=>$paymentLink));
        }
    }

}

