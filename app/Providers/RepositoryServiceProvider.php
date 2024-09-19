<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

use App\Repositories\CountriesRepository;
use App\Repositories\Interfaces\CountriesRepositoryInterface;

use App\Repositories\MasterSettingsRepository;
use App\Repositories\Interfaces\MasterSettingsRepositoryInterface;

use App\Repositories\UserStripeRepository;
use App\Repositories\Interfaces\UserStripeRepositoryInterface;

use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\Interfaces\UserSubscriptionsRepositoryInterface;

use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\Interfaces\UserSubscriptionsHistoryRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(CountriesRepositoryInterface::class, CountriesRepository::class);
        $this->app->bind(MasterSettingsRepositoryInterface::class, MasterSettingsRepository::class);
        $this->app->bind(UserStripeRepositoryInterface::class, UserStripeRepository::class);
        $this->app->bind(UserSubscriptionsRepositoryInterface::class, UserSubscriptionsRepository::class);
        $this->app->bind(UserSubscriptionsHistoryRepositoryInterface::class, UserSubscriptionsHistoryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
