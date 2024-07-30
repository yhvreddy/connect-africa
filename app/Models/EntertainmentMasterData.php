<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\EntertainmentMasterRepositoryInterface as EMDContract;

class EntertainmentMasterData extends Model implements EMDContract
{
    use HasFactory, SoftDeletes;

    protected $table = 'entertainments_master_data';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'is_active',
        'image',
        'other_response'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function image_path($fullPath=false){

        $blank = 'assets/src/images/blank.png';
        $image = $fullPath?asset($blank):$blank;
        if(!empty($this->image) && Storage::exists($this->image)){
            $image = $fullPath?asset(Storage::url($this->image)):Storage::url($this->image);
        }

        return $image;
    }
}
