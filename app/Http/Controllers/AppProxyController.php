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
use Cookie;

class AppProxyController extends Controller
{
    public function proxycalled(Request $request)
    {
        $shop = Auth::user();
        $accessKeysDB=DB::table('insonth')->select('beammid','beamapi','beammode')->where('shop',$shop->name)->first();//->count();//->get();//first();//pluck();//get();//->toArray();
        $uname=$accessKeysDB->beammid;
        $pass=$accessKeysDB->beamapi;
        $beamMode=$accessKeysDB->beammode;
        $authKeyNew = base64_encode($uname . ":" . $pass);
        if($beamMode=='test')
        {
            $urlHit="https://stg-partner-api.beamdata.co/purchases/".$uname;
        }
        elseif($beamMode=='live')
        {
            $urlHit="https://partner-api.beamdata.co/purchases/".$uname;
        }
        else
        {
            dd('error');
        }

        $action=$request->act;
        if(!isset($action))
        {
            dd("Working");
            $statChk=DB::table('beamchkoutorderdata')
                    ->select('stat','beampurchaseid')
                    ->where('id',$request->id)
                    ->first();

            $beampurchaseids=$statChk->beampurchaseid;
            if($statChk->stat == "1")
            {
            }
            if($statChk->stat == "0")
            {
                $urlHit=$urlHit.'/'.$beampurchaseids."/detail";

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

                        $notes=$beampurchaseids;
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

                    $orderdata1 = json_encode($orderdata);
                    $orderdata = json_decode(json_encode($orderdata),true);

                    $shopApi = $shop->api()->rest('POST', '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders.json',$orderdata)['body']['order'];
                    $shopApi = json_decode(json_encode($shopApi), true);
                    $order_id=$shopApi['id'];
                    $order_stat_url=$shopApi['order_status_url'];
                    if($order_stat_url<>'')
                    {
                        $res1 = DB::table('beamchkoutorderdata')
                                        ->where('beampurchaseid',$beampurchaseids)
                                        ->update(['stat' => '1','shoporderid' => $order_id]);
                    }
                    return redirect($order_stat_url);
                }
                elseif($payStatus=="beamed")
                {
                }
            }
        }
        if($action=="btnshowornot")
        {
            $putOnPage=$request->whichbutton;
            $resValue='Show';
            $btnValue="";
            $modalDataBtn="";

            $appUrl=env('APP_URL');
            $stylethis="
                <style>

                    .cart__checkout-button {
                        max-width: 100%!important;
                    }
                    div#btndata {
                        max-width: 44rem;
                    }

                    button#beamcheckoutbutton {
                        width: 100%;
                        height: 40px;
                        border-style: none;
                        border-radius: 10px;
                        background: url(".$appUrl."/storage/img/Primarys.svg) no-repeat;
                        background-size: cover;
                        cursor: pointer !important;
                        margin: 0px auto;
                        margin-top: 10px !important;
                        text-align: center;
                        background-position: center;
                        min-height: calc(4.5rem + var(--buttons-border-width) * 2);
                        min-width: calc(12rem + var(--buttons-border-width) * 2);
                    }


                    @media only screen and ( max-width: 748px) {
                        button#beamcheckoutbutton{
                            width: 100% !important;
                            max-width:445px;
                            height: 50px;
                            border-style: none;
                            border-radius: 10px;
                            background: url(".$appUrl."/storage/img/Primarys.svg) no-repeat;
                            background-size: cover;
                            cursor: pointer !important;
                            margin: 0px auto;
                            margin-top: 10px !important;
                            text-align: center;
                            background-position: center;
                        }
                    }

                    @media only screen and ( max-width: 490px) {
                        button#beamcheckoutbutton{
                            width: 100% !important;
                            max-width:100vw;
                            height: 50px;
                            border-style: none;
                            border-radius: 10px;
                            background: url(".$appUrl."/storage/img/Primarys.svg) no-repeat;
                            background-size: cover;
                            cursor: pointer !important;
                            margin: 0px auto;
                            margin-top: 10px !important;
                            text-align: center;
                            background-position: center;
                        }
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
                }
            }
            elseif($putOnPage=="cart")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgcart')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgcart;
                if($dbShowOrNot=="yes")
                {
                    $btnValue="$stylethis <button name='beamchkoutc' id='beamcheckoutbutton' onclick=\"beambuttoncr();return false;\" class='btn-textc'></button>";
                }
            }
            elseif($putOnPage=="chkout")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgchkout')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgchkout;
                if($dbShowOrNot=="yes")
                {
                    // $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick='beambuttoncr();return false;' type='button' class='cart-notification__close modal__close-button link link--text focus-inset' aria-label='Close'></button>";
                    //$btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick=\"beambuttoncr();return false;\" class='btn-textc' class='cart-notification__close modal__close-button link link--text focus-inset' aria-label='Close'></button>";
                    // $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick='beambuttoncr();return false;' class='cart-notification__close modal__close-button link link--text focus-inset' aria-label='Close'></button>";//btn-textc
                    $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick='beambuttoncr();return false;' type=\"button\" class=\"cart-notification__close modal__close-button link link--text focus-inset\" aria-label=\"Close\"></button>";
                }
            }
            elseif($putOnPage=="chkouta")
            {
                $dbShowOrNotData=DB::table('insonth')->select('pgchkout')->where('shop',$shop->name)->first();
                $dbShowOrNot=$dbShowOrNotData->pgchkout;
                if($dbShowOrNot=="yes")
                {
                    // $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick='beambuttoncr();return false;' type='button' class='cart-notification focus-inset color-background-1 gradient animate' aria-label='Close'></button>";
                    // $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick=\"beambuttoncr();return false;\" class='btn-textc' onmouseup='this.closest(cart-drawer).close()' aria-label='Close'></button>";
                    $btnValue="<button name='beamchkoutc' id='beamcheckoutbutton' onclick='beambuttoncr();return false;' type=\"button\" class=\"cart-notification__close modal__close-button link link--text focus-inset\" aria-label=\"Close\"></button>";
                }
            }
            return response()->json(array('res'=>$resValue,'btn'=>$btnValue));
        }

        if($action=="createurl")
        {
            $ncustid='0';
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
            $urlRedirectHost=$request->urlredhost;
            $orderItemsData='';
            $lastiItem=$ncart_items_number-1;
            $thisID=0;
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
                    'beampurchaseid'=> $thisID,
                    'stat'=>'0',
                    'shoporderid'=>'0',
                ];
                if($a==0)
                {
                    $thisID = DB::table('beamchkoutorderdata')->insertGetId($insertData);
                }
                elseif($a>0)
                {
                    $res = DB::table('beamchkoutorderdata')->insert($insertData);
                }
                DB::commit();
            }
            $emptyField="";
            $currentTime = date("d-m-Y H:i:s");
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
                    "redirectUrl": "'.$urlRedirectHost.'?id='.$thisID.'",
                    "requiredFieldsFormId": "beamdatacompany-checkout"
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
            $newOrderID = DB::table('beamchkoutorderdata')
                ->select('carttoken','created_at')
                ->where('id', $thisID)
                ->first();
            $res3 = DB::table('beamchkoutorderdata')
                ->where('carttoken', $newOrderID->carttoken)
                ->where('stat', '0')
                ->where('created_at', $newOrderID->created_at)
                ->update(['beampurchaseid' => $purchaseId, 'shoporderid' => $ncart_total_price]);
            DB::commit();
            return response()->json(array('paymentLink'=>$paymentLink));
        }
        if($action=="createurlprdct")
        {
            $ncustid='0';
            $proid=$request->pro_variantid;
            $proqty=$request->pro_qty;
            $urlRedirectHost=$request->urlredhost;
            $products = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/variants/'.$proid.'.json');
            $products = $products['body']['variant'];
            $products = json_encode($products);
            $products = json_decode($products, true);
            $productID=$products['product_id'];
            $productsName = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$productID.'.json')['body']['product'];
            $imageID=$products['image_id'];
            if($imageID!=null)
            {
                $imageName = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$productID.'/images/'.$imageID.'.json');
                $imageName = $imageName['body']['image'];
                $imageName = json_encode($imageName);
                $imageName = json_decode($imageName, true);
            }
            else
            {
                $imageName = $shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$productID.'.json');
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
            $ncart_items_name=$productsName['title'];
            $ncart_items_descp=$products['title'];
            if($ncart_items_descp=="Default Title"){$ncart_items_descp="";}
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
            $thisID = DB::table('beamchkoutorderdata')->insertGetId($insertData);
            DB::commit();
            $emptyField="";
            $currentTime = date("d-m-Y H:i:s");
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
                    "redirectUrl": "'.$urlRedirectHost.'?id='.$thisID.'",
                    "requiredFieldsFormId": "beamdatacompany-checkout"
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
            $res1 = DB::table('beamchkoutorderdata')
                ->where('id', $thisID)
                ->update(['beampurchaseid' => $purchaseId, 'shoporderid' => $ncart_total_price]);

            return response()->json(array('paymentLink'=>$paymentLink));
        }
    }

}

