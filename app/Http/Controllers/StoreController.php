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
use Yajra\DataTables\DataTables;

class StoreController extends Controller
{
    public function home()
    {
        $shop = Auth::user();
        $activeThemeID="";
        $activeThemeName="";
        // $themes=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes.json')['body']['themes'];
        $themes=$shop->api()->rest('GET','/admin/api/2022-10/themes.json')['body']['themes'];
        foreach ($themes as $theme)
        {
            if($theme->role=='main')
            {
                $active_theme=$theme;
                $activeThemeID=$theme->id;
                $activeThemeName=$theme->name;
            }
        }

        $inj=DB::table('insonth')->select('id')->where('shop',$shop->name)->where('actthemeid',$activeThemeID)->count();//->count();//->get();//first();//pluck();//get();//->toArray();
        if($inj==0)
        {
            $this->snippetInstall();
            $injStatus="Installed";
            $res=DB::table('insonth')->insert(['shop' => $shop->name, 'actthemeid' => $activeThemeID, 'acttheme' => $activeThemeName, 'pgprod' => 'no', 'pgcart' => 'no', 'pgchkout' => 'no', 'beammid' => 'shopifytest', 'beamapi' => 'ksv6dUbhbHNGVNcfOeyTYv6GTrR5Cm-zKiIx5VEhghM=', 'beammode' => 'test']);
        }
        elseif($inj>0)
        {
            $injStatus="Aleady Installed";
        }
        return view("welcome",compact(['injStatus']));
    }
    public function storeDetails()
    {
        $this->home();
        $colorData=DB::table('colorbtn')->select()->get();
        $colorData = json_decode(json_encode($colorData), true);

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
        $optionData=DB::table('insonth')->select()->where('shop',$shop->name)->where('actthemeid',$active_theme->id)->first();
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
        elseif($tarObj=="pgchkout")
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
        elseif($tarObj=="beammode")
        {
            if($newVal=="live")
            {
                $newVal="test";
            }
            else
            {
                $newVal="live";
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
    public function updateBeamData(Request $request)
    {
        $shop = Auth::user();
        $beamMID=$request->beamid;
        $beamAPI=$request->beamapi;

        $res = DB::table('insonth')
            ->where('shop',$shop->name)
            ->update(['beammid' => $beamMID , 'beamapi' => $beamAPI]);

        if($res)
        {
            return response()->json(['message'=> "Credentials Updated",'status'=>'success']);
        }
        else
        {
            return response()->json(['message'=> "No Changes",'status'=>'info']);
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

        //SETUP EDIT 3 snippets/cart-notification.liquid 27 SIDE
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'snippets/cart-notification.liquid'])['body']['asset']['value'];
        // dd($html);
        $app_include="
            {% comment %} //Checkout Page Start {% endcomment %}
                {% render 'ajaxifycart1' %}
                <div id='btnchkout'>Loading...</div>
                <script>
                    btnshowchkout('chkout');
                </script>
            {% comment %} //Checkout Page End {% endcomment %}\n";
        if(strpos($html,'{% comment %} //Checkout Page Start {% endcomment %}') === false)
        {
            $pos=strpos($html,'<button type="button" class="link button-label">');
            $pos=$pos+94;
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "snippets/cart-notification.liquid",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 3A snippets/cart-drawer.liquid 317
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'snippets/cart-drawer.liquid'])['body']['asset']['value'];
        // dd($html);
        $app_include="
            {% comment %} //Checkout Page A Start {% endcomment %}
                {% render 'ajaxifycart1' %}
                <div id='btnchkout'>Loading...</div>
                <script>
                    btnshowchkout('chkouta');
                </script>
            {% comment %} //Checkout Page A End {% endcomment %}\n";
        if(strpos($html,'{% comment %} //Checkout Page A Start {% endcomment %}') === false)
        {
            $pos=strpos($html,"'sections.cart.checkout'");
            $pos=$pos+66;
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "snippets/cart-drawer.liquid",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 4 assets/global.js 155
        $html=$shop->api()->rest('GET','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',['asset[key]'=>'assets/global.js'])['body']['asset']['value'];
        // dd($html);
        $app_include="\n//NewGlobal1\ndocument.getElementById('selproductqty').value=this.input.value;\n";
        if(strpos($html,'//NewGlobal1') === false)
        {
            $pos=strpos($html,'if (previousValue');
            $pos=$pos+83;
            $newhtml=substr($html,0,$pos) . $app_include . substr($html,$pos);
            $toupdate=[
                "asset" => [
                    "key" => "assets/global.js",
                    "value" => $newhtml
                ]
            ];
            $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$toupdate);
        }

        //SETUP EDIT 5 assets/global.js 856
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
        $fileText="
            {% render 'aaloadcss' %}
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
                                let shopName = 'https://{{ request.host }}/apps/proxy';

                                $.ajax({
                                    type: 'GET',
                                    url: shopName,
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
                                            urlredhost: shopName,
                                        },
                                    success: function (data) {
                                                //var cartClearContents = fetch(window.Shopify.routes.root + 'cart/clear.js');
                                                loader.classList.remove('showLoader');
                                                beambtn.disabled=false;
                                                window.location.href = data.paymentLink;
                                            },
                                    error: function (data) {
                                                loader.classList.remove('showLoader');
                                                beambtn.disabled=false;
                                                window.location.reload();
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
                        let shopName = 'https://{{ request.host }}/apps/proxy';

                        var ID=pro_id;
                        var QTY=qty;

                        $.ajax({
                            type: 'GET',
                            url: shopName,
                            dataType: 'json',
                            data: {
                                    act: 'createurlprdct',
                                    pro_variantid: ID,
                                    pro_qty: QTY,
                                    urlredhost: shopName,
                                },
                            success: function (data) {
                                        loader.classList.remove('showLoader');
                                        beambtn.disabled=false;
                                        window.location.href = data.paymentLink;
                                    },
                            error: function (data) {
                                        loader.classList.remove('showLoader');
                                        beambtn.disabled=false;
                                        window.location.reload();
                                    },
                        });

                    }
                    async function btnshow(whichbutton)
                    {
                        let shopName = 'https://{{ request.host }}/apps/proxy';
                        $.ajax({
                            type: 'GET',
                            url: shopName,
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


        //SETUP ADD 1 snippets/ajaxifycart1.liquid
        $fileText="
            <script src='https://code.jquery.com/jquery-3.3.1.min.js'
            integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='
            crossorigin='anonymous'>
            </script>
            <script>
                async function btnshowchkout(whichbutton)
                    {
                        let shopName = 'https://{{ request.host }}/apps/proxy';
                        $.ajax({
                            type: 'GET',
                            url: shopName,
                            dataType: 'json',
                            data: {
                                    act: 'btnshowornot',
                                    whichbutton: whichbutton,
                                },
                            success: function (data) {
                                        if(data.res=='Show'){
                                        document.getElementById('btnchkout').innerHTML = data.btn;}
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
                "key"=>"snippets/ajaxifycart1.liquid",
                "value"=>"$fileText"
            ]
        ];
        $snippet=$shop->api()->rest('PUT','/admin/api/'.env('SHOPIFY_API_VERSION').'/themes/'.$active_theme->id.'/assets.json',$data_to_put);

        //SETUP ADD 2 snippets/aaloadcss.liquid
        $fileText='
            <div id="loader" class="hidden overlay">
                <font size=5 color=skyblue class="lds-text"><b></b></font>
                <img src="https://media.tenor.com/On7kvXhzml4AAAAj/loading-gif.gif" class="lds-img">
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
                    top: 48%;
                    left: 48%;
                    height:40px;
                    width:40px;
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

}
