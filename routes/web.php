<?php
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AppProxyController;
use App\Http\Controllers\AdminController;




Route::group(['prefix' => 'admin'], function () {
    Route::get('/',[AdminController::class,'home'])->name('admin_login_redirect');
    Route::get('login',[AdminController::class,'login_view'])->name('admin_login');
    Route::post('login',[AdminController::class,'admin_login']);
});
// Route::group(['prefix' => 'admin','middleware'=> ['auth:web','prevent-back-history']], function () {
Route::group(['prefix' => 'admin','middleware'=> ['auth:web']], function () {
    Route::get('dashboard',[AdminController::class,'dashboard']);
    Route::get('logout',[AdminController::class,'logout']);
});
// Route::group(['prefix' => 'customer','middleware'=> ['auth:customer','prevent-back-history']], function () {
Route::group(['prefix' => 'customer','middleware'=> ['auth:customer']], function () {
    Route::get('dashboard',[AdminController::class,'dashboard']);
});


Route::group(['prefix' => '', 'middleware' => ['verify.shopify']], function () {
    Route::get('/' , [StoreController::class,'home'])->name('home');
    Route::get('/StoreDetails' , [StoreController::class,'storeDetails'])->name('Store.Details');
    Route::get('/UpdateDB' , [StoreController::class,'updateOptionData'])->name('Update.DB');
    Route::get('/snippetInstall' , [StoreController::class,'snippetInstall'])->name('snippet.create');

    Route::get('/createorder' , [StoreController::class,'createorder'])->name('createorder');
});

Route::group(['prefix' => '', 'middleware' => ['auth.proxy']], function () {
    Route::get('/proxy', [AppProxyController::class,'proxycalled'])->name('proxy');
});





