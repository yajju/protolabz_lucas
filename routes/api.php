<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Http;

use App\Http\Controllers\StoreController;
use App\Http\Controllers\AppProxyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::group(['prefix' => '', 'middleware' => ['auth.proxy']], function () {
//     Route::get('/proxy', [AppProxyController::class,'proxycalled'])->name('proxy');
// });

