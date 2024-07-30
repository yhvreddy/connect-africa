<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CountriesRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Countries;
class CountriesRepository extends BaseRepository
{
    protected $model;

    public function __construct(Countries $_model){
        $this->model = $_model;
    }

    public function __call($method, $args){
        return call_user_func_array([$this->model, $method], $args);
    }

    public function model(){
        return CountriesRepositoryInterface::class;
    }
}
