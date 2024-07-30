<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\CategorizeAssignedListRepositoryInterface as CategorizeAssignedListContract;

class CategorizeAssignedList extends Model implements CategorizeAssignedListContract
{
    use HasFactory;

    protected $table = 'categorize_assigned_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categorize_id',
        'entertainment_id',
        'type',
        'sort_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
