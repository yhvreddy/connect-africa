<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Interfaces\CategoriesRepositoryInterface as CategoryContract;
class Categories extends Model implements CategoryContract
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'slug',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
