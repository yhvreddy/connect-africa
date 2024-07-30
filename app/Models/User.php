<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Interfaces\UserRepositoryInterface as UserContract;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements UserContract
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'username',
        'mobile',
        'avatar',
        'otp',
        'deleted_at',
        'email_verified_at',
        'is_active',
        'access_code',
        'user_referral_id',
        'country_id',
        'stripe_customer_id',
        'location_availability',
        'referring_members',
        'share_amount',
        'user_caps'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function findByField($requestData){
        return $this->where($requestData);
    }


    //Roles Check
    public function isZq(){
        if($this->role_id === 1){
            return true;
        }

        return false;
    }

    public function isAdmin(){
        if($this->role_id === 2){
            return true;
        }

        return false;
    }

    public function isPartner(){
        if($this->role_id === 3){
            return true;
        }

        return false;
    }

    /**
     * Get the role that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    /**
     * Get the country that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->hasOne(Countries::class, 'id', 'country_id');
    }

    /**
     * Get all of the affiliates for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates(){
        return $this->hasMany(User::class, 'user_referral_id', 'id');
    }
    
    /**
     * Get all of the referredUsers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referredUsers(){
        return $this->hasMany(User::class, 'user_referral_id', 'id');
    }

    /**
     * Get the affiliate associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function affiliate(){
        return $this->hasOne(User::class, 'user_referral_id', 'id');
    }


    public function getAffiliatesReferralByCountry($user_referral_id, $country_id){
        $users = $this->where('country_id', $country_id)->where('user_referral_id', $user_referral_id)->where('is_active', 1)->get();
        return $users;
    }

    /**
     * Get the stripe associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stripe(){
        return $this->hasOne(UserStripe::class, 'user_id');
    }
    
    /**
     * Get the subscription associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription(){
        return $this->hasOne(UserSubscriptions::class, 'user_id');
    }

    /**
     * Get all of the history for the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stripe_history(){
        return $this->hasMany(UserSubscriptionsHistory::class, 'user_id', 'id');
    }
}
