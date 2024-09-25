<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\UserSubscriptionsRepositoryInterface as UserSubscriptionsContract;
use Carbon\Carbon;

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

    /**
     * Get the user that owns the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get the subscription that owns the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get the subscriptionType that owns the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionTypes::class, 'subscription_type_id');
    }

    /**
     * Get the subscriptionPlan that owns the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlans::class, 'subscription_plan_id');
    }

    /**
     * Get the subscriptionPaymentMethod that owns the UserSubscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionPaymentMethod()
    {
        return $this->belongsTo(SubscriptionPaymentMethod::class, 'subscription_payment_id');
    }


    public function isActive()
    {
        $currentDate = Carbon::now();

        // Check payment status
        if ($this->payment_status !== 'paid') {
            return false; // Inactive
        }

        // Check date range
        return $currentDate->between($this->start_date, $this->end_date);
    }
}
