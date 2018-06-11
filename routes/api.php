<?php

use Illuminate\Http\Request;

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

Route::middleware('api')->get('/node/blocks', 'NodeController@getBlocks');
Route::middleware('api')->put('/node/transaction', 'NodeController@putTransaction');
Route::middleware('api')->get('/node/transaction/{hash}', 'NodeController@getTransaction');
