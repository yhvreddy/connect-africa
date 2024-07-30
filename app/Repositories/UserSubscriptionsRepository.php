<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserSubscriptionsRepositoryInterface;
use App\Models\UserSubscriptions;
use App\Repositories\BaseRepository;

class UserSubscriptionsRepository extends BaseRepository
{
    protected $model;

    public function __construct(UserSubscriptions $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return UserSubscriptionsRepositoryInterface::class;
    }

}
