<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CountriesController;

use App\Http\Controllers\CASubscriptionController;
use App\Http\Controllers\APIs\CASubscriptionsController;


Route::get('/', [IndexController::class, 'index'])->name('/');
Route::get('/login/{role}', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'loginAccess'])->name('login.access');
Route::get('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');
Route::post('/forget-password', [AuthController::class, 'forgetPasswordRequest'])->name('forget-password.save');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPasswordRequest'])->name('reset-password.save');

Route::middleware(['zq'])->prefix('zq')->name('zq.')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'zqDashboard'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logOutSession'])->name('logout');

    Route::resource('users', UserController::class);
    Route::get('users/soft-delete/{user}', [UserController::class, 'softDelete'])->name('users.soft-delete');
    Route::get('ajax-data', [UserController::class, 'fetchDataListForAjax'])->name('fetch.data.ajax');
    Route::put('/users/{userId}/{action}', [UserController::class, 'updateStatus'])->name('update.status');

    Route::resource('admins', AdminController::class);
    Route::get('admins/soft-delete/{admin}', [AdminController::class, 'softDelete'])->name('admins.soft-delete');
    Route::get('admin-ajax-data', [AdminController::class, 'adminFetchDataListForAjax'])->name('admins.fetch.data.ajax');
    Route::put('/admins/{adminId}/{action}', [AdminController::class, 'updateStatus'])->name('admins.update.status');

    Route::get('admins/over-view/{admin}', [AdminController::class, 'adminOverview'])->name('admins.overview.details');

    //Countries
    Route::resource('countries', CountriesController::class);
    Route::get('countries/soft-delete/{country}', [CountriesController::class, 'softDelete'])->name('countries.soft-delete');
    Route::get('countries-ajax-data', [CountriesController::class, 'fetchDataListForAjax'])->name('countries.fetch.data.ajax');
    Route::put('/countries/{country}/{action}', [CountriesController::class, 'updateStatus'])->name('countries.update.status');

    Route::get('profile/{zq}', [IndexController::class, 'profileEdit'])->name('profile.edit');
    Route::put('profile/{zq}', [IndexController::class, 'profileUpdate'])->name('profile.update');

    Route::get('settings', [SettingsController::class, 'createOrEditSettings'])->name('settings.site');
    Route::post('settings/save', [SettingsController::class, 'saveSettings'])->name('settings.site.save');
    Route::put('settings/update', [SettingsController::class, 'updateSettings'])->name('settings.site.update');

    Route::prefix('stripe')->namespace('\App\Http\Controllers\Stripe')
        ->name('stripe.')
        ->group(function () {
            Route::prefix('subscription')->name('subscription.')->group(function () {
                Route::get('/cancel/{id}', 'SubscriptionController@cancelStriptSubscriptionOnWeb')->name('cancel');
            });
        });
});

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logOutSession'])->name('logout');

    Route::get('profile/{admin}', [AdminController::class, 'profileEdit'])->name('profile.edit');
    Route::put('profile/{admin}', [AdminController::class, 'profileUpdate'])->name('profile.update');

    Route::resource('users', UserController::class);
    Route::get('users/soft-delete/{user}', [UserController::class, 'softDelete'])->name('users.soft-delete');
    Route::get('ajax-data', [UserController::class, 'fetchDataListForAjax'])->name('fetch.data.ajax');
    Route::put('/users/{userId}/{action}', [UserController::class, 'updateStatus'])->name('update.status');


    Route::prefix('subscriptions')->name('subscription.')->group(function () {
        Route::get('/', [CASubscriptionController::class, 'getSubscriptionsList'])->name('list');
        Route::get('/fetch/data-list', [CASubscriptionController::class, 'fetchSubscriptionsList'])->name('fetch.data.list');
        Route::get('/edit/{subscription}', [CASubscriptionController::class, 'editSubscription'])->name('edit.data');
        Route::post('/update-details/{subscription}', [CASubscriptionController::class, 'updateSubscription'])->name('update.data');
    });
});

Route::get('/subscriptions/types', [CASubscriptionsController::class, 'getSubscriptionTypes'])->name('get.subscription.types');
Route::get('/subscriptions/plans', [CASubscriptionsController::class, 'getSubscriptionPlans'])->name('get.subscription.plans');
Route::get('/subscriptions/payment-methods', [CASubscriptionsController::class, 'paymentMethodsBySubscription'])->name('get.subscription.payment.methods');


Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logOutSession'])->name('web.logout');
});

Route::middleware(['zq_admin'])->name('default.')->group(function () {});

Route::name('default.')->group(function () {
    Route::get('users/details/{user}', [DefaultController::class, 'userDetails'])->name('users.details');
});


// Export
Route::get('export-users/list', [App\Http\Controllers\APIs\v1\UserController::class, 'fetchUsersForExport']);


Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Cache is cleared";
})->name('clear.cache');

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return "Storage linked";
})->name('storage.link');

Route::get('/migrate-refresh', function () {
    Artisan::call('migrate:fresh --seed');
    return "Migration Refreshed";
})->name('migrate.fresh');

Route::get('/migrate', function () {
    Artisan::call('migrate');
    return "Migration Run";
})->name('migrate');

Route::get('/swagger', function () {
    Artisan::call('l5-swagger:generate');
    return "Swagger Refreshed";
})->name('generate.swagger');
