<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\Api\V1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;

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

Route::prefix('v1')->group(function () {

    // admin routes
    Route::prefix('admin')->middleware('auth:sanctum', 'role:admin')->group(function () {
        Route::resource('travels', AdminTravelController::class)->only('store');
    });

    // public routes
    Route::post('login', LoginController::class);
    Route::resource('travels', TravelController::class)->only('index');
    Route::get('travels/{travel:slug}/tours', [TourController::class, 'index'])->name('tours.index');
});