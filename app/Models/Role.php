<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\RoleRepositoryInterface as RoleContract;
class Role extends Model implements RoleContract
{
    use HasFactory;

    protected $table = "roles";

    protected $fillable = [
        'title',
        'slug'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
