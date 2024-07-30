<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Interfaces\EntertainmentRepositoryInterface as EntertainmentContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Entertainment extends Model implements EntertainmentContract
{
    use HasFactory, SoftDeletes;

    protected $table = 'entertainments';

    protected $fillable = [
        'user_id',
        'category_id',
        'event_type_id',
        'title',
        'year',
        'rating',
        'description',
        'imdb_score',
        'google_score',
        'rt_score',
        'poster_image',
        'slug',
        'date',
        'time',
        'is_active',
        'truflix_score',
        'sort_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_active'
    ];

    public function poster_path($fullPath=false){
        $blank = 'assets/src/images/blank.png';
        $poster_image = $fullPath?asset($blank):$blank;
        if(!empty($this->poster_image) && Storage::exists($this->poster_image)){
            $poster_image = $fullPath?asset(Storage::url($this->poster_image)):Storage::url($this->poster_image);
        }

        return $poster_image;
    }

    /**
     * Get the category that owns the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(){
        return $this->belongsTo(Categories::class, 'category_id');
    }

    /**
     * Get the eventType that owns the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventType(){
        return $this->belongsTo(EntertainmentMasterData::class, 'event_type_id');
    }

    /**
     * Get all of the getAdditionalData for the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAdditionalDataByType($type){
        return $this->hasMany(EntertainmentAdditionalDetails::class, 'entertainment_id')->where('type', $type);
    }


    public function getAssignedCategorizedList($type){
        return $this->hasMany(CategorizeAssignedList::class, 'entertainment_id')->where('type', $type);
    }

    /**
     * Get all of the categorizedList for the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categorizedList(){
        return $this->hasMany(CategorizeAssignedList::class, 'entertainment_id', 'id');
    }

    /**
     * Get the user that owns the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all of the getAdditionalData for the Entertainment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAdditionalData(){
        return $this->hasMany(EntertainmentAdditionalDetails::class, 'entertainment_id');
    }
}
