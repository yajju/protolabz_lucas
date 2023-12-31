<?php
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AppProxyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MessengerController;




Route::group(['prefix' => 'admin'], function () {
    Route::get('/',[AdminController::class,'home'])->name('admin_login');
    Route::get('login',[AdminController::class,'login_view'])->name('login');
    Route::post('login',[AdminController::class,'logged']);
});
Route::group(['prefix' => 'admin','middleware'=> ['auth:web','prevent-back-history']], function () {
    Route::get('dashboard',[AdminController::class,'dashboard']);
    Route::get('logout',[AdminController::class,'logout']);
    Route::get('change-password', [AdminController::class, 'change_passwordform']);
    Route::post('change-password', [AdminController::class, 'change_password']);

    Route::get('merchants',[AdminController::class,'merchants']);
    Route::get('transactions',[AdminController::class,'transactions']);
    Route::get('reports',[AdminController::class,'reports']);
    Route::get('support',[AdminController::class,'support']);
    Route::get('documentation',[AdminController::class,'documentation']);

    Route::post('/update-status/{user}', [AdminController::class,'updateStatus'])->name('update.status');


});

Route::group(['prefix' => 'merchant'], function () {
    Route::get('/',[MerchantController::class,'home'])->name('merchant_login');
    Route::get('login',[MerchantController::class,'login_view'])->name('login');
    Route::post('login',[MerchantController::class,'logged']);
    Route::get('registration', [MerchantController::class, 'registration_form'])->name('registration');
    Route::post('registration', [MerchantController::class, 'register']); 
});
Route::group(['prefix' => 'merchant','middleware'=> ['auth:merchant','prevent-back-history','is_approved']], function () {
    Route::get('dashboard',[MerchantController::class,'dashboard']);
    Route::get('logout',[MerchantController::class,'logout']);
    Route::get('change-password', [MerchantController::class, 'change_passwordform']);
    Route::post('change-password', [MerchantController::class, 'change_password']);

    // Route::get('forgot-password', [MerchantController::class, 'forgot_password']);

    Route::get('profile',[MerchantController::class,'profile']);
    Route::post('profileupdate/{id}', [MerchantController::class, 'profileupdate']);
    Route::get('transactions',[MerchantController::class,'transactions']);
    Route::get('reports',[MerchantController::class,'reports']);
    Route::get('support',[MerchantController::class,'support']);
    Route::get('documentation',[MerchantController::class,'documentation']);


    Route::get('messenger', [MessengerController::class,'index'])->name('merchant.messenger.index');
    Route::get('messenger/create', [MessengerController::class,'createTopic'])->name('merchant.messenger.createTopic');
    Route::post('messenger', [MessengerController::class,'storeTopic'])->name('merchant.messenger.storeTopic');
    Route::get('messenger/inbox', [MessengerController::class,'showInbox'])->name('merchant.messenger.showInbox');
    Route::get('messenger/outbox', [MessengerController::class,'showOutbox'])->name('merchant.messenger.showOutbox');
    Route::get('messenger/{topic}', [MessengerController::class,'showMessages'])->name('merchant.messenger.showMessages');
    Route::delete('messenger/{topic}', [MessengerController::class,'destroyTopic'])->name('merchant.messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', [MessengerController::class,'replyToTopic'])->name('merchant.messenger.reply');
    Route::get('messenger/{topic}/reply', [MessengerController::class,'showReply'])->name('merchant.messenger.showReply');
    
});


Route::group(['prefix' => '', 'middleware' => ['verify.shopify']], function () {
    Route::get('/' , [StoreController::class,'home'])->name('home');
    // Route::get('/' , [StoreController::class,'storeDetails'])->name('home');
    Route::get('/StoreDetails' , [StoreController::class,'storeDetails'])->name('Store.Details');
    Route::get('/UpdateDB' , [StoreController::class,'updateOptionData'])->name('Update.DB');
    Route::get('/UpdateBeamCr' , [StoreController::class,'updateBeamData'])->name('Update.BEAM');
    Route::get('/snippetInstall' , [StoreController::class,'snippetInstall'])->name('snippet.create');


    // Route::get('/beamsuperadmin' , [StoreController::class,'beamSuperAdmin'])->name('beam.super.admin');
    // Route::get('/createorder' , [StoreController::class,'createorder'])->name('createorder');
});

Route::group(['prefix' => '', 'middleware' => ['auth.proxy']], function () {
    Route::get('/proxy', [AppProxyController::class,'proxycalled'])->name('proxy');
});






