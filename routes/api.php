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
    Route::post('/logs', 'LogController@store');

    Route::get('/reports/requests-by-consumer', 'ReportController@generateRequestsByConsumer');
    Route::get('/reports/requests-by-service', 'ReportController@generateRequestsByService');
    Route::get('/reports/average-latencies-by-service', 'ReportController@generateAverageLatenciesByService');
});