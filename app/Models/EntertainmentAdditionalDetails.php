<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Interfaces\EntertainmentAdditionalRepositoryInterface as EDRContract;
use Illuminate\Support\Facades\Storage;
class EntertainmentAdditionalDetails extends Model implements EDRContract
{
    use HasFactory;

    protected $table = 'entertainments_additional_details';

    protected $fillable = [
        'entertainment_id',
        'type',
        'title',
        'image',
        'description',
        'url',
        'em_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the entertainment that owns the EntertainmentAdditionalDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entertainment(){
        return $this->belongsTo(Entertainment::class, 'entertainment_id');
    }

    /**
     * Get the master associated with the EntertainmentAdditionalDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function master(){
        return $this->hasOne(EntertainmentMasterData::class, 'id', 'em_id');
    }

    public function image_path($fullPath=false){

        $blank = 'assets/src/images/blank.png';
        $image = $fullPath?asset($blank):$blank;
        if(!empty($this->image) && Storage::exists($this->image)){
            $image = $fullPath?asset(Storage::url($this->image)):Storage::url($this->image);
        }

        return $image;
    }    
}
