<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\UserSubscriptionsHistoryRepositoryInterface as UserSubscriptionsHistoryContract;

class UserSubscriptionsHistory extends Model implements UserSubscriptionsHistoryContract
{
    use HasFactory;

    protected $table = 'users_subscriptions_history';

    protected $fillable = [
        'user_id',
        'user_subscription_id',
        'price_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'status',
        'response_data',
        'subscription_id',
        'subscription_status',
        'payment_status',
        'type',
        'payment_status',
        'amount',
        'tax_amount',
        'total_amount',
        'next_due_date',
        'customer_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user that owns the UserSubscriptionsHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
