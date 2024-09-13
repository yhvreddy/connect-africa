<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\v1\AuthController;
use App\Http\Controllers\APIs\v1\MoviesController;
use App\Http\Controllers\APIs\v1\ShowsController;
use App\Http\Controllers\APIs\v1\EventsController;
use App\Http\Controllers\APIs\v1\UserController;
use App\Http\Controllers\APIs\v1\SettingsController;
use App\Http\Controllers\APIs\v1\EntertainmentController;
use App\Http\Controllers\APIs\EntertainmentMasterDataController;
use App\Http\Controllers\APIs\CASubscriptionsController;

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

    Route::resource('users', UserController::class);

    Route::get('settings/site', [SettingsController::class, 'settings']);

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'registerNewUser']);
        Route::post('forget-pin', [AuthController::class, 'forgetPin']);
    });

    Route::post('user-verify-token', [AuthController::class, 'verifyUserToken']);

    Route::prefix('subscriptions')->group(function () {
        Route::get('/list', [CASubscriptionsController::class, 'getSubscriptions']);
        Route::get('/type-list/{subscription}', [CASubscriptionsController::class, 'getSubscriptionTypes']);
        Route::get('/plans-list/{subscriptionTypeId}', [CASubscriptionsController::class, 'getSubscriptionPlans']);
        Route::get('/payment-methods', [CASubscriptionsController::class, 'paymentMethods']);
        Route::get('/payment-methods/{subscription}', [CASubscriptionsController::class, 'paymentMethodsBySubscription']);
    });


    Route::middleware(['auth:sanctum'])->group(function () {

        Route::prefix('subscriptions')->group(function () {
            Route::post('/create', [CASubscriptionsController::class, 'createSubscriptions']);
        });

        Route::get('user/details', [AuthController::class, 'getUserDetails']);
        Route::post('user/update-details', [AuthController::class, 'updateUserDetails']);
        Route::post('auth/logout', [AuthController::class, 'logOutUser']);
    });
});
