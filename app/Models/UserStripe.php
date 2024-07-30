<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\UserStripeRepositoryInterface as UserStripeContract;

class UserStripe extends Model implements UserStripeContract
{
    use HasFactory;

    protected $table = 'users_stripe';

    protected $fillable = [
        'user_id',
        'customer_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
