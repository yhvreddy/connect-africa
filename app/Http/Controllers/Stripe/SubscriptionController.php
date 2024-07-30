<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserSubscriptionsRepository;
use App\Repositories\UserSubscriptionsHistoryRepository;
use App\Repositories\UserStripeRepository;
use App\Repositories\UserRepository;
use Stripe\Stripe;
use App\Http\Requests\Subscriptions\CreateRequest;
use App\Http\Requests\Subscriptions\CancelRequest;
use App\Http\Requests\Subscriptions\PaymentRequest;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\Log;
use App\Services\StripeHelper as StripeService;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    use HttpResponses, TruFlix;

    public function __construct(
        UserSubscriptionsRepository $_userSubscriptions,
        UserSubscriptionsHistoryRepository $_userSubscriptionHistory,
        UserStripeRepository $_userStripe,
        UserRepository $_user
    ){
        $this->userStripe = $_userStripe;
        $this->user = $_user;
        $this->userSubscriptions = $_userSubscriptions;
        $this->userSubscriptionHistory = $_userSubscriptionHistory;

        $this->stripe = new \Stripe\StripeClient(config('app.stripe_secret'));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stripe/subscription/create",
     *     tags={"Stripe"},
     *     summary="Create Subscription",
     *     @OA\Parameter(
     *         name="price_id",
     *         in="query",
     *         description="Price Id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=201,
     *          description="Subscription as created successful",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function create(CreateRequest $_request, StripeService $_stripe){

        if(!$_request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $_request->user();
        if(!$user){
            return $this->validation('Unauthenticated.');
        }

        try{
            $userSubscription = $this->userSubscriptions->where('user_id', $user->id)->first();
            if(!empty($userSubscription->subscription_status) && $userSubscription->subscription_status === 'active'){
                return $this->success('You Have Already Subscribed');
            }
            
            $stripe_customer_id = $user->stripe_customer_id;
            if(empty($stripe_customer_id)){
                // Create Customer in Stripe
                $customerDataForStripe  =   [
                    'name' => $user->name,
                    'email' => $user->email,
                ];
                $createUserInStripe =   $this->stripe->customers->create($customerDataForStripe);
                if($createUserInStripe->id){
                    $user->stripe_customer_id = $createUserInStripe->id;
                    $user->save();

                    $this->userStripe->create([
                        'user_id'   =>  $user->id,
                        'customer_id'   =>  $user->stripe_customer_id ?? $createUserInStripe->id
                    ]);

                    $stripe_customer_id = $createUserInStripe->id;
                }
            }

            if($stripe_customer_id){
                
                //Create Subscription in Stripe
                $subscriptionDataForStripe = [
                    'customer' => $stripe_customer_id,
                    'items' => [
                        ['price' => $_request->price_id]
                    ],
                    'payment_behavior' => 'default_incomplete',
                    'expand' => ['latest_invoice.payment_intent'],
                ];
                $subscription  =   $this->stripe->subscriptions->create($subscriptionDataForStripe);
                if($subscription->id){

                    $amount = $subscription->plan->amount? number_format($subscription->plan->amount / 100, 2) : 0.0;
                    $tax_amount = 0.0;

                    if(!$userSubscription){
                        $userSubscription = $this->userSubscriptions->create([
                            'user_id'               =>  $user->id,
                            'price_id'              =>  $_request->price_id,
                            'subscription_id'       =>  $subscription->id,
                            'response_data'         =>  json_encode($subscription),
                            'subscription_status'   =>  $subscription->status ?? 'incomplete',
                            'payment_status'        =>  'pending',
                            'customer_name'         =>  $user->name,
                            'amount'                =>  $amount,
                            'total_amount'          =>  $amount,
                            'tax_amount'            =>  $tax_amount
                        ]);
                    }else{
                        $userSubscription->price_id             = $_request->price_id;
                        $userSubscription->amount               = $amount;
                        $userSubscription->total_amount         = $amount;
                        $userSubscription->tax_amount           = $tax_amount;
                        $userSubscription->subscription_id      = $subscription->id;
                        $userSubscription->response_data        = json_encode($subscription);
                        $userSubscription->subscription_status  = $subscription->status ?? 'incomplete';
                        $userSubscription->payment_status       = 'pending';
                        $userSubscription->save();
                    }

                    if($userSubscription){
                        $_stripe->createStripeHistory($userSubscription, $subscription, 'create');
                    }

                    return $this->objectCreated('Subscription Created Successfully.', [
                        'subscriptionId'    =>  $subscription->id,
                        'clientSecret'      =>  $subscription->latest_invoice->payment_intent->client_secret
                    ]);
                }

                return $this->validation('Sorry, Failed To Create Subscription.');
            }
            
            return $this->validation('Invalid Request To Create Subscription.');

        }catch (\Throwable $th) {
            return $this->internalServer($th->getMessage());
        } 
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stripe/subscription/cancel",
     *     tags={"Stripe"},
     *     summary="Cancel Subscription",
     *     @OA\Parameter(
     *         name="subscription_id",
     *         in="query",
     *         description="Subscription Id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=201,
     *          description="Subscription as cancelled successful",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function cancel(CancelRequest $_request, StripeService $_stripe){
        if(!$_request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $_request->user();
        if(!$user){
            return $this->validation('Unauthenticated.');
        }

        try{
            $getSubscription = $this->userSubscriptions->where(['subscription_id' => $_request->subscription_id])->first();
            $subscription = $this->stripe->subscriptions->retrieve($getSubscription->subscription_id, []);
            if($subscription && $getSubscription){
                $cancel = $this->stripe->subscriptions->cancel($subscription->id, []);
                if($cancel){
                    $getSubscription->status                =   1;
                    $getSubscription->subscription_status   =   $cancel->status ?? 'cancelled';
                    $getSubscription->payment_status        =   $cancel->status ?? 'cancelled';
                    $getSubscription->response_data         =   json_encode($cancel);
                    $getSubscription->save();

                    $user->is_active = 0;
                    $user->save();

                    $_stripe->createStripeHistory($getSubscription, $cancel, 'cancel');
        
                    return $this->objectCreated('Subscription Cancelled Successfully.', [
                        'id'        =>  $cancel->id,
                        'status'    =>  $cancel->status
                    ]);
                }

                return $this->validation('Failed To Cancel Subscription.');
            }

            return $this->validation('Invalid Request To Cancel Subscription.');

        }catch (\Throwable $th) {
            return $this->internalServer($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stripe/subscription/payment-status",
     *     tags={"Stripe"},
     *     summary="Payment Subscription Status",
     *     @OA\Parameter(
     *         name="subscription_id",
     *         in="query",
     *         description="Subscription Id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="payment_id",
     *         in="query",
     *         description="Payment Id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Response(
     *          response=201,
     *          description="Subscription Details",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function paymentStatus(PaymentRequest $_request, StripeService $_stripe){
        if(!$_request->wantsJson()){
            return $this->validation('Invalid request format.');
        }

        $user = $_request->user();
        if(!$user){
            return $this->validation('Unauthenticated.');
        }

        try{
            $subscription = $user->subscription;
            if($subscription->subscription_id !== $_request->subscription_id){
                return $this->validation('Invalid Subscription Details.');
            }

            $payment = $this->stripe->paymentIntents->retrieve($_request->payment_id, []);
            if($payment){
                $paymentStatus  =   $payment->status;
                $_stripe->createStripeHistory($subscription, $payment, 'payment');

                if($paymentStatus === 'succeeded'){

                    $invoiceId  =   $payment->invoice;
                    $invoice =  $this->stripe->invoices->retrieve($invoiceId, []);
                    $invoiceStatus  =   $invoice->status;

                    $planPeriod = $invoice->lines->data[0]->period;

                    $period_start = $this->convertDateTimeFormTimestamp($planPeriod->start);
                    $period_end = $this->convertDateTimeFormTimestamp($planPeriod->end);

                    $subscription->subscription_status = 'active';
                    $subscription->payment_status = $invoiceStatus;
                    $subscription->starts_at = $period_start ?? null;
                    $subscription->ends_at = $period_end ?? null;
                    $subscription->save();

                    $_stripe->createStripeHistory($subscription, $invoice, 'invoice');

                    return $this->objectCreated('Subscription Details', [
                        'subscription_status'   =>  $subscription->subscription_status,
                        'payment_status'        =>  $subscription->payment_status,
                        'plan_duration'         =>  ['start' => $period_start, 'end' => $period_end]
                    ]);
                }
            }
            
            return $this->validation('Invalid Payment Details.');

        }catch (\Throwable $th) {
            return $this->internalServer($th->getMessage());
        }
    }




    // Web Functionality
    public function cancelStriptSubscriptionOnWeb($id, StripeService $_stripe){
        $user = $this->user->find($id);

        if(!isset($user->subscription)){
            return $this->validation('No Subscription details found.');
        }

        if($user->subscription->subscription_status !== 'active' || empty($user->subscription->subscription_id)){
            return $this->validation('Invalid subscription to cancel.');
        }
        
        try{
            $getSubscription = $this->userSubscriptions->where(['subscription_id' => $user->subscription->subscription_id])->first();
            $subscription = $this->stripe->subscriptions->retrieve($getSubscription->subscription_id, []);
            if($subscription && $getSubscription){
                $cancel = $this->stripe->subscriptions->cancel($subscription->id, []);
                if($cancel){
                    $getSubscription->status                =   1;
                    $getSubscription->subscription_status   =   $cancel->status ?? 'cancelled';
                    $getSubscription->response_data         =   json_encode($cancel);
                    $getSubscription->save();

                    $_stripe->createStripeHistory($getSubscription, $cancel, 'cancel');
        
                    return $this->objectCreated('Subscription Cancelled Successfully.', [
                        'id'        =>  $cancel->id,
                        'status'    =>  $cancel->status
                    ]);
                }

                return $this->validation('Failed To Cancel Subscription.');
            }

            return $this->validation('Invalid Request To Cancel Subscription.');

        }catch (\Throwable $th) {
            return $this->internalServer($th->getMessage());
        }
    }
}
