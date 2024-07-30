<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\UserSubscriptionsRepositoryInterface as UserSubscriptionsContract;

class UserSubscriptions extends Model implements UserSubscriptionsContract
{
    use HasFactory;

    protected $table = 'users_subscriptions';

    protected $fillable = [
        'user_id',
        'price_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'status',
        'response_data',
        'subscription_id',
        'subscription_status',
        'payment_status',
        'amount',
        'tax_amount',
        'total_amount',
        'next_due_date',
        'customer_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'response_data'
    ];


    /**
     * Get all of the history for the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history(){
        return $this->hasMany(UserSubscriptionsHistory::class, 'user_subscription_id', 'id');
    }
}
