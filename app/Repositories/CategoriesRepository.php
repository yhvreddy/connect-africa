<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CategoriesRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Categories;
class CategoriesRepository extends BaseRepository
{
    protected $model;

    public function __construct(Categories $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return CategoriesRepositoryInterface::class;
    }
}
