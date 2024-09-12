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
        'subscription_id',
        'subscription_type_id',
        'subscription_plan_id',
        'subscription_payment_id',
        'amount',
        'type',
        'start_date',
        'end_date',
        'status',
        'payment_status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    /**
     * Get all of the history for the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(UserSubscriptionsHistory::class, 'user_subscription_id', 'id');
    }
}
