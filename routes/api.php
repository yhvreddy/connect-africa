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

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::get('user/details', [AuthController::class, 'getUserDetails']);
        Route::post('auth/logout', [AuthController::class, 'logOutUser']);

        Route::prefix('stripe')->namespace('\App\Http\Controllers\Stripe')->group(function () {
            // Stripe Payments Routes
            Route::get('/', 'IndexController@index');

            Route::prefix('subscription')->group(function (){
                Route::post('/create', 'SubscriptionController@create');
                Route::post('/cancel', 'SubscriptionController@cancel');
                Route::post('/payment-status', 'SubscriptionController@paymentStatus');
            });
        });
    });

    Route::prefix('stripe')->namespace('\App\Http\Controllers\Stripe')->group(function () {
        Route::post('/webhook', 'IndexController@webhook');
    });

    Route::prefix('entertainment')->group(function () {

        Route::prefix('master')->group(function () {
            Route::get('genres', [EntertainmentMasterDataController::class, 'getGenresList']);
            Route::get('ott-platforms', [EntertainmentMasterDataController::class, 'getOttPlatFormList']);
            Route::get('event-types', [EntertainmentMasterDataController::class, 'getEventTypeList']);
        });

        Route::resource('movies', MoviesController::class);
        Route::get('movies/categorized/list', [MoviesController::class, 'categorizedList']);

        Route::resource('shows', ShowsController::class);
        Route::get('shows/categorized/list', [ShowsController::class, 'categorizedList']);

        Route::resource('events', EventsController::class);

        Route::get('get-categorized/{type}', [EntertainmentController::class, 'categorizedList']);
    });
});
