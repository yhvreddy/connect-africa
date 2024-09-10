<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlans extends Model
{
    use HasFactory;

    protected $table = "subscriptions_plans";

    protected $fillable = [
        'name',
        'type',
        'amount',
        'subscription_type_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionTypes::class, 'subscription_type_id');
    }
}
