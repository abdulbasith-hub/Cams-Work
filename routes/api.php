<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\ClientApIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | is assigned the "api" middleware group. Enjoy building your API!
 * |
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/auditschedule/{apicode}', [APIController::class, 'auditscheduledel'])
->name('auditschedule');

// ///////////////////////////////////////////////////////////////////////////////////////////////

Route::post('/getslipdetails', [APIController::class, 'getslipdetails'])->middleware(\App\Http\Middleware\JWTAuth::class);
Route::post('/generateToken', [APIController::class, 'generateToken']);

Route::post('/readyforauditdata', [APIController::class, 'readyforauditdata'])
->middleware(\App\Http\Middleware\JWTAuth::class);

// ///////////////////////////////////////////////////////////////////////////////////////////////
