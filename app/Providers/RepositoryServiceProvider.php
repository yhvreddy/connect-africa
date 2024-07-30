<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\EntertainmentAdditionalRepository;
use App\Repositories\Interfaces\EntertainmentAdditionalRepositoryInterface;

use App\Repositories\EntertainmentMasterRepository;
use App\Repositories\Interfaces\EntertainmentMasterRepositoryInterface;

use App\Repositories\EntertainmentRepository;
use App\Repositories\Interfaces\EntertainmentRepositoryInterface;

use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

use App\Repositories\CategoriesRepository;
use App\Repositories\Interfaces\CategoriesRepositoryInterface;

use App\Repositories\CountriesRepository;
use App\Repositories\Interfaces\CountriesRepositoryInterface;

use App\Repositories\MasterSettingsRepository;
use App\Repositories\Interfaces\MasterSettingsRepositoryInterface;

use App\Repositories\CategorizeRepository;
use App\Repositories\Interfaces\CategorizeRepositoryInterface;

use App\Repositories\CategorizeAssignedListRepository;
use App\Repositories\Interfaces\CategorizeAssignedListRepositoryInterface;

use App\Repositories\UserStripeRepository;
use App\Repositories\Interfaces\UserStripeRepositoryInterface;

use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\Interfaces\UserSubscriptionsRepositoryInterface;

use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\Interfaces\UserSubscriptionsHistoryRepositoryInterface;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\EloquentRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(CategoriesRepositoryInterface::class, CategoriesRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(EntertainmentRepositoryInterface::class, EntertainmentRepository::class);
        $this->app->bind(EntertainmentMasterRepositoryInterface::class, EntertainmentMasterRepository::class);
        $this->app->bind(EntertainmentAdditionalRepositoryInterface::class, EntertainmentAdditionalRepository::class);
        $this->app->bind(CountriesRepositoryInterface::class, CountriesRepository::class);
        $this->app->bind(MasterSettingsRepositoryInterface::class, MasterSettingsRepository::class);
        $this->app->bind(CategorizeAssignedListRepositoryInterface::class, CategorizeAssignedListRepository::class);
        $this->app->bind(CategorizeRepositoryInterface::class, CategorizeRepository::class);
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
