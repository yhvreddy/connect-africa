<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CategorizeRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Categorize;
class CategorizeRepository extends BaseRepository
{
    protected $model;

    public function __construct(Categorize $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return CategorizeRepositoryInterface::class;
    }
}
