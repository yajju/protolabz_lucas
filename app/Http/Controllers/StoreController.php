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

use App\Models\ColorsDB;

class StoreController extends Controller
{
    public function home()
    {
        echo "welcome";
    }
    // public function home()
    // {
    //     $shop = Auth::user();
    //     $activeThemeID="";
    //     $activeThemeName="";
    //     $themes=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes.json')['body']['themes'];
    //     foreach ($themes as $theme)
    //     {
    //         if($theme->role=='main')
    //         {
    //             $activeThemeID=$theme->id;
    //             $activeThemeName=$theme->name;
    //         }
    //     }
    //     $inj=DB::table('insonth')->select('id')->where('shop',$shop->name)->where('actthemeid',$activeThemeID)->count();//->count();//->get();//first();//pluck();//get();//->toArray();
    //     if($inj==0)
    //     {
    //         $this->snippetInstall();
    //         $injStatus="Installed";
    //         $res=DB::table('insonth')->insert(['shop' => $shop->name, 'actthemeid' => $activeThemeID, 'acttheme' => $activeThemeName, 'pgprod' => 'no', 'pgcart' => 'no']);
    //     }
    //     elseif($inj>0)
    //     {
    //         $injStatus="Aleady Installed";
    //     }
    //     return view("welcome",compact(['injStatus']));
    // }
    public function storeDetails()
    {
        $colorData=DB::table('colorbtn')->select()->get();
        $colorData = json_decode(json_encode($colorData), true);
        $shop = Auth::user();
        $optionData=DB::table('insonth')->select()->where('shop',$shop->name)->first();
        $optionData = json_decode(json_encode($optionData), true);
        return view("storeDetails",compact(['colorData','optionData']));
    }
    public function updateOptionData(Request $request)
    {
        $shop = Auth::user();
        $newVal=$request->newval;
        $tarObj=$request->tarobj;

        if($tarObj=="pgprod")
        {
            if($newVal=="no")
            {
                $newVal="yes";
            }
            else
            {
                $newVal="no";
            }
        }
        elseif($tarObj=="pgcart")
        {
            if($newVal=="no")
            {
                $newVal="yes";
            }
            else
            {
                $newVal="no";
            }
        }

        $res = DB::table('insonth')
            ->where('shop',$shop->name)
            ->update([$tarObj => $newVal]);

        if($res)
        {
            return response()->json(['message'=> "Updated",'status'=>'success']);
        }
        else
        {
            return response()->json(['message'=> "Error Caught",'status'=>'error']);
        }
    }
    public function snippetInstall()
    {
        $shop = Auth::user();
        $active_theme="";
        $themes=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes.json')['body']['themes'];
        foreach ($themes as $theme)
        {
            if($theme->role=='main')
            {
                $active_theme=$theme;
            }
        }

        //SETUP EDIT 1 sections/main-product.liquid 642
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'sections/main-product.liquid'])['body']['asset']['value'];
        // dd($html);
        $app_include="{% comment %} //Product Page Start {% endcomment %}
            {% render 'ajaxify-cart' %}
            <input type='hidden' id='selectedproduct' value='{{ product.selected_or_first_available_variant.id }}'>
            <input type='hidden' id='selproductqty' value='1'>
            <div id='btndata'>Loading...</div>
            <script>
                btnshow('product');
            </script>
            {% comment %} //Product Page End {% endcomment %}\n";
        if(strpos($html,'{% comment %} //Product Page Start {% endcomment %}') === false)
        {
            $pos=strpos($html,'</product-form>');
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "sections/main-product.liquid",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 2 sections/main-cart-footer.liquid 74
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'sections/main-cart-footer.liquid'])['body']['asset']['value'];
        // dd($html);
        $app_include="{% comment %} //Cart Page Start {% endcomment %}
            {% render 'ajaxify-cart' %}
            <div id='btndata'>Loading...</div>
            <script>
                btnshow('cart');
            </script>
            {% comment %} //Cart Page End {% endcomment %}\n";
        if(strpos($html,'{% comment %} //Cart Page Start {% endcomment %}') === false)
        {
            $pos=strpos($html,'<div id="cart-errors">');
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "sections/main-cart-footer.liquid",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 3 assets/global.js 155
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'assets/global.js'])['body']['asset']['value'];
        // dd($html);
        $app_include="\n//NewGlobal1\ndocument.getElementById('selproductqty').value=this.input.value;\n";
        if(strpos($html,'//NewGlobal1') === false)
        {
            $pos=strpos($html,'if (previousValue');
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "assets/global.js",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 4 assets/global.js 856
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'assets/global.js'])['body']['asset']['value'];
        // dd($html);
        $app_include="\n//NewGlobal2\ndocument.getElementById('selectedproduct').value=this.currentVariant.id;\n";
        if(strpos($html,'//NewGlobal2') === false)
        {
            $pos=strpos($html,'this.toggleAddButton(!this');
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "assets/global.js",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP ADD 1 snippets/ajaxify-cart.liquid
        $fileText="{% render 'aaloadcss' %}
            <script src='https://code.jquery.com/jquery-3.3.1.min.js'
                    integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='
                    crossorigin='anonymous'></script>
            <script>
                    function beambuttoncr()
                    {
                        const beambtn = document.getElementById('beamcheckoutbutton');
                        beambtn.disabled=true;
                        const loader = document.getElementById('loader');
                        loader.classList.add('showLoader');

                        var cartitemsid=[];
                        var cartitemsqty=[];
                        var cartitemsfinalprice=[];
                        var cartitemsimg=[];
                        var cartitemsname=[];
                        var cartitemsdescp=[];
                        var cartitems;
                        var carttoken;
                        var carttotal_price;

                        var cartContents = fetch(window.Shopify.routes.root + 'cart.js')
                        .then(response => response.json())
                        .then(data => {
                                carttoken=data.token;
                                carttotal_price=data.items_subtotal_price;
                                cartitems=data.items.length;
                                for(a=0;a<data.items.length;a++)
                                {
                                    cartitemsid[a]=data.items[a].id;
                                    cartitemsqty[a]=data.items[a].quantity;
                                    cartitemsfinalprice[a]=data.items[a].final_line_price;
                                    cartitemsimg[a]=data.items[a].image;
                                    cartitemsname[a]=data.items[a].product_title;
                                    cartitemsdescp[a]=data.items[a].variant_title;
                                }

                                $.ajax({
                                    type: 'GET',
                                    url: 'https://".$shop->name."/apps/proxy',
                                    dataType: 'json',
                                    data: {
                                            act: 'createurl',
                                            cart_token: carttoken,
                                            cart_total_price: carttotal_price,
                                            cart_items_number: cartitems,
                                            cart_items_finalprice: cartitemsfinalprice,
                                            cart_items_images: cartitemsimg,
                                            cart_items_name: cartitemsname,
                                            cart_items_descp: cartitemsdescp,
                                            cart_items_qty: cartitemsqty,
                                            cart_items_variantid: cartitemsid,
                                        },
                                    success: function (data) {
                                                var cartClearContents = fetch(window.Shopify.routes.root + 'cart/clear.js');
                                                window.location.href = data.paymentLink;
                                            },
                                    error: function (data) {
                                                console.log('Error '+data);
                                            },
                                });

                            });
                    }
                    function addItemNew(pro_id,qty)
                    {
                        const beambtn = document.getElementById('beamcheckoutbutton');
                        beambtn.disabled=true;
                        const loader = document.getElementById('loader');
                        loader.classList.add('showLoader');

                        var ID=pro_id;
                        var QTY=qty;

                        $.ajax({
                            type: 'GET',
                            url: 'https://".$shop->name."/apps/proxy',
                            dataType: 'json',
                            data: {
                                    act: 'createurlprdct',
                                    pro_variantid: ID,
                                    pro_qty: QTY,
                                },
                            success: function (data) {
                                        window.location.href = data.paymentLink;
                                    },
                            error: function (data) {
                                        console.log('Error '+data);
                                    },
                        });

                    }
                    async function btnshow(whichbutton)
                    {
                        $.ajax({
                            type: 'GET',
                            url: 'https://".$shop->name."/apps/proxy',
                            dataType: 'json',
                            data: {
                                    act: 'btnshowornot',
                                    whichbutton: whichbutton,
                                },
                            success: function (data) {
                                        if(data.res=='Show'){
                                        document.getElementById('btndata').innerHTML = data.btn;}
                                    },
                            error: function (data) {
                                        console.log('Error '+data.res);
                                    },
                          });
                    }

            </script>
                ";
        $data_to_put=[
            "asset"=>[
                "key"=>"snippets/ajaxify-cart.liquid",
                "value"=>"$fileText"
            ]
        ];
        $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$data_to_put);

        //SETUP ADD 2 snippets/aaloadcss.liquid
        $fileText='
            <div id="loader" class="hidden overlay">
                <font size=5 color=skyblue class="lds-text"><b>Authenticating</b></font>
                <img src="https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Swing-Preloader.svg" class="lds-img">
                <style>
                .showLoader{
                    display:block !important;
                }
                .lds-text {
                    font-family: "Georgia", "Times New Roman", "Garamond", Serif;
                    position: absolute;
                    top: 30%;
                    left: 48%;
                    margin: -50px 0px 0px -50px;
                }
                .lds-img {
                    position: absolute;
                    top: 40%;
                    left: 50%;
                    margin: -50px 0px 0px -50px;
                }
                .overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100vh;
                    background: rgba(0,0,0,.8);
                    z-index: 999;
                    opacity: 1;
                    transition: all 0.5s;
                }
                </style>
            </div>';
        $data_to_put=[
            "asset"=>[
                "key"=>"snippets/aaloadcss.liquid",
                "value"=>"$fileText"
            ]
        ];
        $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$data_to_put);
    }
    public function createorder()
    {
        $cartOrderDataB=DB::table('beamcustomer')
                            ->where('beampurchaseid','VmCqbjMdTA')
                            ->where('order_status','pending')
                            ->get();
        // $cartOrderDataB[0]->shop;
        // $customer_email="protolabzajaymohali@gmail.com";
        // $order_status="partially_paid";
        $order_status="paid";
        // $customer_note="please deleiver order between 10AM to 5PM";

        $orderdata['order']['customer'] = array(
                "first_name" => "".$cartOrderDataB[0]->cu_first_name."",
                "last_name" => "".$cartOrderDataB[0]->cu_last_name."",
                "email" => "".$cartOrderDataB[0]->customer_email.""
        );
        $orderdata['order']['billing_address'] = array(
            "first_name" => "".$cartOrderDataB[0]->bl_first_name."",
            "last_name" => "".$cartOrderDataB[0]->bl_last_name."",
            "address1" => "".$cartOrderDataB[0]->bl_address."",
            "phone" => "".$cartOrderDataB[0]->bl_phone."",
            "city" => "".$cartOrderDataB[0]->bl_city."",
            "province" => "".$cartOrderDataB[0]->bl_province."",
            "country" => "".$cartOrderDataB[0]->bl_country."",
            "zip" => "".$cartOrderDataB[0]->bl_zip.""
        );
        $orderdata['order']['shipping_address'] = array(
            "first_name" => "".$cartOrderDataB[0]->dl_first_name."",
            "last_name" => "".$cartOrderDataB[0]->dl_last_name."",
            "address1" => "".$cartOrderDataB[0]->dl_address."",
            "phone" => "".$cartOrderDataB[0]->dl_phone."",
            "city" => "".$cartOrderDataB[0]->dl_city."",
            "province" => "".$cartOrderDataB[0]->dl_province."",
            "country" => "".$cartOrderDataB[0]->dl_country."",
            "zip" => "".$cartOrderDataB[0]->dl_zip.""
        );
        $orderdata['order']['note'] = $cartOrderDataB[0]->customer_note;
        $orderdata['order']['email'] = $cartOrderDataB[0]->customer_email;
        $orderdata['order']['transactions'] = array(
            [
                "kind" => "authorization",
                "status" => "success",
                "amount" => $cartOrderDataB[0]->order_amt
            ]
        );
        $orderdata['order']['financial_status'] = $order_status;

    }
    public function createorder_OK()
    {
        $shop = Auth::user();

        $customer_email="protolabzajaymohali@gmail.com";
        // $order_status="partially_paid";
        $order_status="paid";
        $customer_note="please deleiver order between 10AM to 5PM";

        $orderdata['order']['line_items'] = array(
            ["variant_id" => "43686093029695", "quantity" => 1],
            ["variant_id" => "43686093095231", "quantity" => 2]
        );
        $orderdata['order']['customer'] = array(
                "first_name" => "Ajay",
                "last_name" => "Kumar",
                "email" => "protolabzajaymohali@gmail.com"
        );
        $orderdata['order']['billing_address'] = array(
            "first_name" => "Anju",
            "last_name" => "Chauhan",
            "address1" => "New Market",
            "phone" => "8219391666",
            "city" => "YamunaNagar",
            "province" => "Haryana",
            "country" => "India",
            "zip" => "135001"
        );
        $orderdata['order']['shipping_address'] = array(
            "first_name" => "Veer",
            "last_name" => "Singh",
            "address1" => "#182/7 Gunnu Ghat, Sirmour",
            "phone" => "8219391668",
            "city" => "Nahan",
            "province" => "Himachal Pradesh",
            "country" => "India",
            "zip" => "173001"
        );
        $orderdata['order']['note'] = $customer_note;
        $orderdata['order']['email'] = $customer_email;
        $orderdata['order']['transactions'] = array(
            [
                "kind" => "authorization",
                "status" => "success",
                "amount" => 50.0
            ]
        );
        $orderdata['order']['financial_status'] = $order_status;



        $order = array('order'=>array(
            'line_items'=>array(
                array("variant_id" => "43686093029695", "quantity" => 1),
                array("variant_id" => "43686093095231", "quantity" => 2)
            )
            ));
        // echo'<pre>';
        // print_r($orderdata);
        // print_r($order);
        // die('32');
        // $orderdata = json_decode(json_encode($orderdata, true));
        // $orderdata = json_encode($orderdata,true);
        // $orderdata = json_decode($orderdata);
        $orderdata = json_decode(json_encode($orderdata),true);
        // dd($orderdata);
        // Log::info("Data : " . json_encode($orderdata));
        // $shopApi = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products.json',[$orderdata]);//['order'];
        $shopApi = $shop->api()->rest('POST', '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders.json',$orderdata);//['order'];
       // Log::info("Res : " . json_encode($shopApi));
        $shopApi = json_decode(json_encode($shopApi, true));
        echo'<pre>';
        print_r($shopApi);
        echo'</pre>';
       /// return view("productsorder",compact(['shopApi']));
        // $order_id=$shopApi[0]->id;
        // $order_stat_url=$shopApi[0]->order_status_url;

        // return redirect($order_stat_url);
    }

}
