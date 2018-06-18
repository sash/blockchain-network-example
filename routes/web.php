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
if ($_ENV['APPLICATION'] == 'node'){
    Route::get('/', function () {
        return view('welcome');
    });
}


if ($_ENV['APPLICATION'] == 'wallet') {
    Route::get('/', function () {
        return view('wallet');
    });
}

if ($_ENV['APPLICATION'] == 'explorer') {
    // Explorer endpoints
}
