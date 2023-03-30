<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\MallController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SpotParkirController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::controller(LoginRegisterController::class)->group(function () {
    Route::get('/api/login', function(){return abort(404);});
    Route::get('/api/register', function(){return abort(404);});
    Route::post('/api/login', 'handleLogin');
    Route::post('/api/loginEncrypted', 'handleLoginEncrypted');
    Route::post('/api/register', 'handleRegister');
});

Route::controller(OrderController::class)->group(function () {
    Route::get('/api/order', function(){return abort(404);});
    Route::get('/api/order/cancel', function(){return abort(404);});
    Route::post('/api/order/checkin', 'handleCheckin');
    Route::post('/api/order/checkout', 'handleCheckout');
    Route::post('/api/order', 'handleOrder');
    Route::post('/api/order/cancel', 'cancelOrder');
    Route::get('/api/orders/{id}', 'readOrder');
    Route::post('/api/orders/{id}', 'readOrder');
    Route::post('/api/order/check', 'checkCard');
});

Route::controller(MallController::class)->group(function () {
    Route::get('/api', 'readMall');
    Route::post('/api', 'readMall');
    Route::get('/api/readMall/{id}', 'detailMall');
    Route::post('/api/readMall/{id}', 'detailMall');
    Route::get('/api/readMall/{id}/bookingStatus', 'readMallBooked');
    Route::post('/api/readMall/{id}/bookingStatus', 'readMallBooked');
    Route::get('/api/createMall', function(){return abort(404);});
    Route::post('/api/createMall', 'createMall');
    Route::get('/api/deleteMall', function(){return abort(404);});
    Route::post('/api/deleteMall', 'deleteMall');
});

Route::controller(SpotParkirController::class)->group(function () {
    Route::get('/api/spot/{id}', 'showSpotDetail');
    Route::post('/api/spot/{id}', 'showSpotDetail');
    Route::post('/api/spot/update/car', 'setCarExist');
});

require __DIR__.'/auth.php';
