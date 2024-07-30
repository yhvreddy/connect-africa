<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MasterSettingsRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\MasterSettings;

class MasterSettingsRepository extends BaseRepository
{
    protected $model;

    public function __construct(MasterSettings $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return MasterSettingsRepositoryInterface::class;
    }


    public function getDetailsByType($type){
        return $this->model->where('type', $type);
    }
}
