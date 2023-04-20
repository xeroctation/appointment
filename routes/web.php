<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('appointment');
});



Route::group(['middleware' => 'auth'], function () {
    Route::controller(AppointmentController::class)->group(function() {
        Route::get('calendar', 'index')->name('appointment');
        Route::post('store', 'store')->name('store');
        Route::get('service_provider', 'serviceProvider')->name('service_provider');
    });
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();
