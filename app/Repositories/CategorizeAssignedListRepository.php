<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CategorizeAssignedListRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\CategorizeAssignedList;
class CategorizeAssignedListRepository extends BaseRepository
{
    protected $model;

    public function __construct(CategorizeAssignedList $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return CategorizeAssignedListRepositoryInterface::class;
    }
}
