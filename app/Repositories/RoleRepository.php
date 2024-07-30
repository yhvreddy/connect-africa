<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Role;
class RoleRepository extends BaseRepository
{
    protected $model;

    public function __construct(Role $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return RoleRepositoryInterface::class;
    }
}
