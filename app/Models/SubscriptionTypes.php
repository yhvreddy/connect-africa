<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionTypes extends Model
{
    use HasFactory;

    protected $table = "subscriptions_types";

    protected $fillable = [
        'name',
        'subscription_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get all of the subscriptionPlans for the subscriptionPlans
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlans::class, 'subscription_type_id', 'id');
    }
}
