<?php

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

use Illuminate\Http\Request;

if ($_ENV['APPLICATION'] == 'node'){
    Route::get('/', function () {
        return view('node', ['routes' => Route::getRoutes()]);
    });
}

if ($_ENV['APPLICATION'] == 'faucet') {
    Route::get('/', 'FaucetController@getFaucet');
    Route::post('/', 'FaucetController@postFaucet');
    
}


if ($_ENV['APPLICATION'] == 'wallet') {
    Route::get('/', function () {
        return view('wallet');
    });
}

if ($_ENV['APPLICATION'] == 'explorer') {
    Route::get('/{any}', function () {
        return view('explorer');
    })->where('any', '.*');
}
