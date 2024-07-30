<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EntertainmentAdditionalRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\EntertainmentAdditionalDetails;

class EntertainmentAdditionalRepository extends BaseRepository
{
    protected $model;

    public function __construct(EntertainmentAdditionalDetails $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return EntertainmentAdditionalRepositoryInterface::class;
    }
}
