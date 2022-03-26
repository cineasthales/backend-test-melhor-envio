<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Api'], function()
{
    Route::get('/logs', 'LogController@store'); // TODO: change to POST
    Route::get('/authenticated-entities/report/requests', 'AuthenticatedEntityController@generateRequestsReport');
    Route::get('/services/report/requests', 'ServiceController@generateRequestsReport');
    Route::get('/services/report/latencies', 'ServiceController@generateLatenciesReport');
});