<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserSubscriptionsHistoryRepositoryInterface;
use App\Models\UserSubscriptionsHistory;
use App\Repositories\BaseRepository;

class UserSubscriptionsHistoryRepository extends BaseRepository
{
    protected $model;

    public function __construct(UserSubscriptionsHistory $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return UserSubscriptionsHistoryRepositoryInterface::class;
    }

}
