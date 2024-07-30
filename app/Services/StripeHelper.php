<?php
namespace App\Services;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use App\Repositories\UserSubscriptionsHistoryRepository;

class StripeHelper {

    use HttpResponses, TruFlix;

    protected $userSubscriptionHistory;

    public function __construct(
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory,
    ){
        $this->userSubscriptionHistory = $_userSubscriptionHistory;
        $this->stripe = new \Stripe\StripeClient(config('app.stripe_secret'));
    }

    public function createStripeHistory($subscription, $response, $type){
        return $this->userSubscriptionHistory->create([
            'user_id'               =>  $subscription->user_id,
            'user_subscription_id'  =>  $subscription->id,
            'price_id'              =>  $subscription->price_id,
            'subscription_id'       =>  $subscription->subscription_id,
            'response_data'         =>  json_encode($response),
            'status'                =>  $subscription->status,
            'subscription_status'   =>  $subscription->subscription_status,
            'payment_status'        =>  $subscription->payment_status,
            'starts_at'             =>  $subscription->starts_at,
            'ends_at'               =>  $subscription->ends_at,
            'type'                  =>  $type,
            'amount'                =>  $subscription->amount,
            'tax_amount'            =>  $subscription->tax_amount,
            'total_amount'          =>  $subscription->total_amount,
            'next_due_date'         =>  $subscription->next_due_date,
            'customer_name'         =>  $subscription->customer_name
        ]);
    }

}