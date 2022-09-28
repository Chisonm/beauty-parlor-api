<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Shop\ShopController;
use App\Http\Controllers\Api\Appointment\AppointmentController;

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
Route::prefix('v1')->group(function () {
    Route::post('signup', [AuthController::class, 'register']);
    Route::post('signin', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('shops', [ShopController::class, 'getAllShop']);
        Route::get('shops/{shop}', [ShopController::class, 'getShop']);
        Route::post('/create-shop', [ShopController::class, 'createShop']);
        Route::put('/update-shop/{shop}', [ShopController::class, 'updateShop']);
        Route::delete('/delete-shop/{shop}', [ShopController::class, 'deleteShop']);

        Route::get('appointments', [AppointmentController::class, 'getAllAppointment']);
        Route::post('appointments', [AppointmentController::class, 'createAppointment']);
        Route::get('appointment/{id}', [AppointmentController::class, 'getAppointmentById']);
        // update appointment status
        Route::put('appointment/{id}', [AppointmentController::class, 'updateAppointmentStatus']);
        // get appointment history by appointment id
        Route::get('appointment-history/{id}', [AppointmentController::class, 'getAppointmentHistoryById']);
        // get appointment by id
        Route::get('appointment-history', [AppointmentController::class, 'getAppointmentHistory']);
        // get a single user appointment history
        Route::get('appointment-user-history/{id}', [AppointmentController::class, 'getAppointmentHistoryByUserId']);
});

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
