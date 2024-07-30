<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EntertainmentRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Entertainment;

class EntertainmentRepository extends BaseRepository
{
    protected $model;

    public function __construct(Entertainment $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return EntertainmentRepositoryInterface::class;
    }
}
