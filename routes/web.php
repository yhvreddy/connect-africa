<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShowsController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GenresController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\OttController;
use App\Http\Controllers\CategorizeController;
use App\Http\Controllers\CountriesController;


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


    Route::resource('genres', GenresController::class);
    Route::get('genres/soft-delete/{genre}', [GenresController::class, 'softDelete'])->name('genres.soft-delete');
    Route::get('genre-ajax-data', [GenresController::class, 'genreFetchDataListForAjax'])->name('genres.fetch.data.ajax');
    Route::put('/genres/{genreId}/{action}', [GenresController::class, 'updateStatus'])->name('genres.update.status');

    //Countries
    Route::resource('countries', CountriesController::class);
    Route::get('countries/soft-delete/{country}', [CountriesController::class, 'softDelete'])->name('countries.soft-delete');
    Route::get('countries-ajax-data', [CountriesController::class, 'fetchDataListForAjax'])->name('countries.fetch.data.ajax');
    Route::put('/countries/{country}/{action}', [CountriesController::class, 'updateStatus'])->name('countries.update.status');


    //ott
    Route::resource('ott', OttController::class);
    Route::get('otts/soft-delete/{ott}', [OttController::class, 'softDelete'])->name('otts.soft-delete');
    Route::get('ott-ajax-data', [OttController::class, 'ottFetchDataListForAjax'])->name('otts.fetch.data.ajax');
    Route::put('/otts/{ottId}/{action}', [OttController::class, 'updateStatus'])->name('otts.update.status');

    //Categorize
    Route::resource('categorizes', CategorizeController::class);
    Route::get('categorizes/soft-delete/{categorize}', [CategorizeController::class, 'softDelete'])->name('categorizes.soft-delete');
    Route::get('categorize-ajax-data', [CategorizeController::class, 'fetchDataListForAjax'])->name('categorizes.fetch.data.ajax');
    Route::put('/categorizes/{categorizeId}/{action}', [CategorizeController::class, 'updateStatus'])->name('categorizes.update.status');


    //event-type
    Route::resource('event-type', EventTypeController::class);
    Route::get('event-types/soft-delete/{event-type}', [EventTypeController::class, 'softDelete'])->name('event-types.soft-delete');
    Route::get('event-type-ajax-data', [EventTypeController::class, 'eventTypeFetchDataListForAjax'])->name('event-type.fetch.data.ajax');
    Route::put('/event-type/{eventTypeId}/{action}', [EventTypeController::class, 'updateStatus'])->name('event-type.update.status');

    Route::resource('partners', PartnerController::class);
    Route::get('deactivated/partners-list', [PartnerController::class, 'deactivatedList'])
        ->name('partners.deactivated.list');
    Route::get('partners/soft-delete/{partner}', [PartnerController::class, 'softDelete'])->name('partners.soft-delete');
    Route::get('partner-ajax-data', [PartnerController::class, 'partnerFetchDataListForAjax'])->name('partner.fetch.data.ajax');
    Route::put('/partners/{partnerId}/{action}', [PartnerController::class, 'updateStatus'])->name('partner.update.status');

    //payment
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('payments/load-data-ajax', [PaymentController::class, 'fetchTransactionsList'])->name('fetch.data.ajax');
    });

    //Affiliates routes
    Route::prefix('affiliates')->name('affiliates.')->group(function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('index');
        Route::get('/create', [AffiliateController::class, 'create'])->name('create');
        Route::post('/', [AffiliateController::class, 'store'])->name('store');
        Route::get('/{affiliate}/edit', [AffiliateController::class, 'edit'])->name('edit');
        Route::put('/{affiliate}', [AffiliateController::class, 'update'])->name('update');
        Route::get('/show/{affiliate}', [AffiliateController::class, 'show'])->name('show');
        Route::get('/show/users/{affiliate}/{type}', [AffiliateController::class, 'showUserDetails'])->name('show.users');

        Route::get('deactivated-list', [AffiliateController::class, 'deactivatedList'])
        ->name('deactivated.list');


        Route::get('soft-delete/{affiliate}', [AffiliateController::class, 'softDelete'])->name('soft-delete');
        Route::get('ajax-data', [AffiliateController::class, 'fetchDataListForAjax'])->name('fetch.data.ajax');
        Route::get('users/ajax-data', [AffiliateController::class, 'fetchAffiliateUsersListForAjax'])->name('users.fetch.data.ajax');
        Route::put('/affiliate/{affiliateId}/{action}', [AffiliateController::class, 'updateStatus'])->name('update.status');

    });

    Route::get('profile/{zq}', [IndexController::class, 'profileEdit'])->name('profile.edit');
    Route::put('profile/{zq}', [IndexController::class, 'profileUpdate'])->name('profile.update');

    Route::get('settings', [SettingsController::class, 'createOrEditSettings'])->name('settings.site');
    Route::post('settings/save', [SettingsController::class, 'saveSettings'])->name('settings.site.save');
    Route::put('settings/update', [SettingsController::class, 'updateSettings'])->name('settings.site.update');

    Route::prefix('stripe')->namespace('\App\Http\Controllers\Stripe')
    ->name('stripe.')
    ->group(function () {
        Route::prefix('subscription')->name('subscription.')->group(function (){
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
});
/*
Route::middleware(['partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'partnerDashboard'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logOutSession'])->name('logout');

    Route::get('territory/users', [PartnerController::class, 'territoryUsers'])->name('territory.users');
    Route::get('territory/users/ajax-data', [PartnerController::class, 'fetchTerritoryUsersListForAjax'])->name('territory.users.fetch.data.ajax');

    Route::get('affiliates', [PartnerController::class, 'affiliates'])->name('affiliate');
    Route::get('affiliates/ajax-data', [PartnerController::class, 'affiliateListForAjax'])->name('affiliate.fetch.data.ajax');

    Route::get('profile/{partner}', [PartnerController::class, 'profileEdit'])->name('profile.edit');
    Route::put('profile/{partner}', [PartnerController::class, 'profileUpdate'])->name('profile.update');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'partnersPayments'])->name('index');
        Route::get('payments/load-data-ajax', [PaymentController::class, 'fetchTransactionsList'])->name('fetch.data.ajax');
    });
});*/

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logOutSession'])->name('web.logout');
});

Route::middleware(['zq_admin'])->name('default.')->group(function () {
    Route::resource('movies', MoviesController::class);
    Route::get('movies/delete-video/{video}', [MoviesController::class, 'deleteVideoSource'])->name('movies.video.delete');
    Route::get('ajax-data', [MoviesController::class, 'fetchDataListForAjax'])->name('fetch.data.ajax');
    Route::put('/movie/{movieId}/{action}', [MoviesController::class, 'updateStatus'])->name('movie.update.status');
    //Categories Type
    Route::get('/movies/categorize-list/{categorize}', [MoviesController::class, 'categorizeMovies'])->name('movies.categorize.list');


    Route::resource('shows', ShowsController::class);
    Route::get('shows/delete/video/{video}', [ShowsController::class, 'deleteVideoSource'])->name('shows.video.delete');
    Route::get('show-ajax-data', [ShowsController::class, 'showFetchDataListForAjax'])->name('show.fetch.data.ajax');
    Route::put('/show/{showId}/{action}', [ShowsController::class, 'updateStatus'])->name('show.update.status');
    //Categories Type
    Route::get('/shows/categorize-list/{categorize}', [ShowsController::class, 'categorizeShows'])->name('shows.categorize.list');

    //Default update order number
    Route::get('/entertainment/update-order/{type}', [MoviesController::class, 'updateOrderOfEntertainment'])->name('entertainment.order.update');

    Route::resource('events', EventsController::class);
    Route::get('event-ajax-data', [EventsController::class, 'eventFetchDataListForAjax'])->name('event.fetch.data.ajax');
    Route::put('/event/{eventId}/{action}', [EventsController::class, 'updateStatus'])->name('event.update.status');
});

Route::name('default.')->group(function () {
    Route::get('users/details/{user}', [DefaultController::class, 'userDetails'])->name('users.details');

    Route::get('territory/details/{territory}', [DefaultController::class, 'territoryDetails'])->name('territory');

    Route::get('territory/users/{territory}', [DefaultController::class, 'territoryUsers'])->name('territory.users');
    Route::get('territory/ajax-list/{territory}', [DefaultController::class, 'territoryUsersList'])->name('territory.users.list');

    Route::get('get-users/{affiliate}/{type}', [DefaultController::class, 'showUserDetails'])->name('show.users');
    Route::get('get-users/ajax-data', [DefaultController::class, 'fetchAffiliateUsersListForAjax'])->name('show.users.fetch.data.ajax');

    Route::get('affiliates/show/{affiliate}', [DefaultController::class, 'affiliateDetailView'])->name('affiliate.detail.view');

    Route::prefix('affiliates')->name('affiliates.')->group(function () {
        Route::get('/list/{id}', [AffiliateController::class, 'index'])->name('index');
        Route::get('/show/{affiliate}', [AffiliateController::class, 'show'])->name('show');
        Route::get('/show/users/{affiliate}/{type}', [AffiliateController::class, 'showUserDetails'])->name('show.users');
        Route::get('ajax-data/{id}', [AffiliateController::class, 'fetchDataListForAjax'])->name('fetch.data.ajax');
        Route::get('users/ajax-data', [AffiliateController::class, 'fetchAffiliateUsersListForAjax'])->name('users.fetch.data.ajax');
    });

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
