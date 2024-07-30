<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserStripeRepositoryInterface;
use App\Models\UserStripe;
use App\Repositories\BaseRepository;

class UserStripeRepository extends BaseRepository
{
    protected $model;

    public function __construct(UserStripe $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return UserStripeRepositoryInterface::class;
    }

}
