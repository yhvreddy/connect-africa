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

class CASubscriptionsController extends Controller
{
    use HttpResponses;

    protected Subscription $subscriptions;
    protected SubscriptionTypes $subscriptionTypes;
    protected SubscriptionPlans $subscriptionPlans;
    protected PaymentMethods $paymentMethods;
    protected SubscriptionPaymentMethod $subscriptionPaymentMethods;

    public function __construct(
        Subscription $_subscriptions,
        SubscriptionTypes $_subscriptionTypes,
        SubscriptionPlans $_subscriptionPlans,
        PaymentMethods $_paymentMethods,
        SubscriptionPaymentMethod $_subscriptionPaymentMethods
    ) {
        $this->subscriptions = $_subscriptions;
        $this->subscriptionTypes = $_subscriptionTypes;
        $this->subscriptionPlans = $_subscriptionPlans;
        $this->paymentMethods = $_paymentMethods;
        $this->subscriptionPaymentMethods = $_subscriptionPaymentMethods;
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
}
