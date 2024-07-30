<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EntertainmentMasterRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\EntertainmentMasterData;
class EntertainmentMasterRepository extends BaseRepository 
{
    protected $model;

    public function __construct(EntertainmentMasterData $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return EntertainmentMasterRepositoryInterface::class;
    }
}
