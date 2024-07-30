<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Interfaces\MasterSettingsRepositoryInterface as MasterSettingsContract;

class MasterSettings extends Model implements MasterSettingsContract
{
    use HasFactory;

    protected $table = "master_settings";

    protected $fillable = [
        'title',
        'description',
        'image',
        'mobile',
        'phone',
        'email',
        'type',
        'logo',
        'fav_icon',
        'copyrights',
        'url',
        'link'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
