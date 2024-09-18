<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\HttpResponses;

use App\Models\Subscription;
use App\Models\SubscriptionTypes;
use App\Models\SubscriptionPlans;
use App\Models\PaymentMethods;
use App\Models\SubscriptionPaymentMethod;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Http\Requests\CASubscriptions\CreateRequest;
use App\Http\Requests\CASubscriptions\UpdateRequest;
use Carbon\Carbon;

class CASubscriptionsController extends Controller
{
    use HttpResponses;

    protected Subscription $subscriptions;
    protected SubscriptionTypes $subscriptionTypes;
    protected SubscriptionPlans $subscriptionPlans;
    protected PaymentMethods $paymentMethods;
    protected SubscriptionPaymentMethod $subscriptionPaymentMethods;
    protected User $users;
    protected UserSubscriptions $userSubscription;

    public function __construct(
        Subscription $_subscriptions,
        SubscriptionTypes $_subscriptionTypes,
        SubscriptionPlans $_subscriptionPlans,
        PaymentMethods $_paymentMethods,
        SubscriptionPaymentMethod $_subscriptionPaymentMethods,
        User $_user,
        UserSubscriptions $_userSubscription
    ) {
        $this->subscriptions = $_subscriptions;
        $this->subscriptionTypes = $_subscriptionTypes;
        $this->subscriptionPlans = $_subscriptionPlans;
        $this->paymentMethods = $_paymentMethods;
        $this->subscriptionPaymentMethods = $_subscriptionPaymentMethods;
        $this->users = $_user;
        $this->userSubscription = $_userSubscription;
    }

    public function getSubscriptions()
    {
        $subscriptions = $this->subscriptions->get();
        return $this->success('Subscriptions List', $subscriptions);
    }

    public function getSubscriptionTypes($subscription)
    {
        $subscriptionTypes = $this->subscriptionTypes->with('subscription')
            ->where('subscription_id', $subscription)->get();
        return $this->success('Subscriptions Types List', $subscriptionTypes);
    }

    public function getSubscriptionPlans($subscriptionTypeId)
    {
        $subscriptionPlans = $this->subscriptionPlans->with('subscriptionType')->where('subscription_type_id', $subscriptionTypeId)->get();
        return $this->success('Subscriptions Plans List', $subscriptionPlans);
    }

    public function paymentMethods()
    {
        $paymentMethods = $this->paymentMethods->get();
        return $this->success('Payment Methods List', $paymentMethods);
    }

    public function paymentMethodsBySubscription($subscription)
    {
        $paymentMethods = $this->subscriptionPaymentMethods
            ->with('paymentMethod')
            ->where('subscription_id', $subscription)->get();
        return $this->success('Payment Methods By Subscription List', $paymentMethods);
    }

    public function createSubscriptions(CreateRequest $request)
    {
        if (!$request->wantsJson()) {
            return $this->validation('Invalid request format.');
        }

        $user = $request->user();
        if ($user->id !== $request->user_id) {
            return $this->validation('Invalid user to create subscription.');
        }

        $subscription = $this->subscriptions->find($request->subscription_id);
        $subscriptionType = $this->subscriptionTypes->find($request->subscription_type_id);
        $subscriptionPlan = $this->subscriptionPlans->find($request->subscription_plan_id);
        $subscriptionPayment = $this->subscriptionPaymentMethods->find($request->subscription_payment_id);
        if ($user && $subscription && $subscriptionType && $subscriptionPlan && $subscriptionPayment) {
            if ($user->subscription) {
                if ($user->subscription->status == 'inactive') {
                    return $this->validation('Your subscription as expired.', $user->subscription);
                }

                if ($user->subscription->status == 'pending') {
                    return $this->validation('Your subscription pending from admin.', $user->subscription);
                }

                return $this->validation('You have already subscribed.', $user->subscription);
            }

            $data = [
                'user_id'   =>  $user->id,
                'subscription_id'   =>  $subscription->id,
                'subscription_type_id'   =>  $subscriptionType->id,
                'subscription_plan_id'   =>  $subscriptionPlan->id,
                'subscription_payment_id'   =>  $subscriptionPayment->id,
                'amount'    =>  $subscriptionPlan->amount,
                'type'    =>  strtolower(str_replace(' ', '-', $subscriptionPlan->type)),
                'status'    =>  'pending'
            ];

            $userSubscription = $this->userSubscription->create($data);
            if ($userSubscription) {
                return $this->success('Subscription created successfully.', $userSubscription);
            }

            return $this->validation('Subscription Failed.', $user->subscription);
        }

        return $this->validation('Invalid Request.');
    }

    public function updateSubscriptions(UpdateRequest $request)
    {
        if (!$request->wantsJson()) {
            return $this->validation('Invalid request format.');
        }

        $user = $request->user();
        if ($user->id !== $request->user_id) {
            return $this->validation('Invalid user to update subscription.');
        }

        $userSubscription = $this->userSubscription->where('user_id', $user->id)->first();
        if (!$userSubscription) {
            return $this->validation('User didn\'t found any subscription.');
        }

        $subscription = $this->subscriptions->find($request->subscription_id);
        $subscriptionType = $this->subscriptionTypes->find($request->subscription_type_id);
        $subscriptionPlan = $this->subscriptionPlans->find($request->subscription_plan_id);
        $subscriptionPayment = $this->subscriptionPaymentMethods->find($request->subscription_payment_id);
        if ($user && $subscription && $subscriptionType && $subscriptionPlan && $subscriptionPayment) {

            $userSubscription->user_id   = $user->id;
            $userSubscription->subscription_id   =  $subscription->id;
            $userSubscription->subscription_type_id   =  $subscriptionType->id;
            $userSubscription->subscription_plan_id   =  $subscriptionPlan->id;
            $userSubscription->subscription_payment_id   =  $subscriptionPayment->id;
            $userSubscription->amount    =  $subscriptionPlan->amount;
            $userSubscription->type   =  strtolower(str_replace(' ', '-', $subscriptionPlan->type));
            $userSubscription->status    =  'inactive';
            $userSubscription->payment_status = 'unpaid';
            $userSubscription->start_date = null;
            $userSubscription->end_date = null;

            if ($userSubscription->save()) {
                return $this->success('Subscription upgraded successfully.', $userSubscription);
            }

            return $this->validation('Subscription not upgraded successfully.', $user->subscription);
        }

        return $this->validation('Invalid Request to upgrade subscription.');
    }
}
