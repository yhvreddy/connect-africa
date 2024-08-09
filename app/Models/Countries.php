<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\CountriesRepositoryInterface as CountryContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Countries extends Model implements CountryContract
{
    use HasFactory, SoftDeletes;

    protected $table = "countries";

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
