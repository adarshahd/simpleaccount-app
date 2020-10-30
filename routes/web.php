<?php

use App\Http\Controllers\Api\AppSettingController;
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
    if(AppSettingController::isApplicationInitialized()) {
        return redirect('/admin');
    }

    return redirect('/onboard');
});

Route::get('/onboard', [\App\Http\Controllers\OnboardController::class, 'index'])->where('any', '.*');

//Route::get('/admin/{any}', 'OnboardController@index')->where('any', '.*');
