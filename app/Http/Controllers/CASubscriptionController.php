<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSubscriptions;
use App\Models\Subscription;
use App\Models\SubscriptionTypes;
use App\Models\SubscriptionPlans;
use App\Models\PaymentMethods;
use App\Models\SubscriptionPaymentMethod;
use App\Http\Requests\CASubscriptions\UpdateRequest;
use Carbon\Carbon;

class CASubscriptionController extends Controller
{
    protected UserSubscriptions $userSubscription;
    protected Subscription $subscriptions;
    protected SubscriptionTypes $subscriptionTypes;
    protected SubscriptionPlans $subscriptionPlans;
    protected PaymentMethods $paymentMethods;
    protected SubscriptionPaymentMethod $subscriptionPaymentMethods;

    public function __construct(
        UserSubscriptions $_userSubscriptions,
        Subscription $_subscriptions,
        SubscriptionTypes $_subscriptionTypes,
        SubscriptionPlans $_subscriptionPlans,
        PaymentMethods $_paymentMethods,
        SubscriptionPaymentMethod $_subscriptionPaymentMethods,
    ) {
        $this->userSubscription = $_userSubscriptions;
        $this->subscriptions = $_subscriptions;
        $this->subscriptionTypes = $_subscriptionTypes;
        $this->subscriptionPlans = $_subscriptionPlans;
        $this->paymentMethods = $_paymentMethods;
        $this->subscriptionPaymentMethods = $_subscriptionPaymentMethods;
    }

    public function getSubscriptionsList(Request $request)
    {
        return view('subscription.index');
    }

    public function fetchSubscriptionsList(Request $request)
    {
        // Defined Columns
        $columnArray = [
            'id',
            'name',
            'email',
            'status',
            'id',
            'id',
            'id',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');

        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $userSubscriptions = $this->userSubscription; // Initialize $users here

        $totalData = $userSubscriptions->count();
        $totalFiltered = $totalData;

        // Filter Data
        if (!empty($data['search'])) {
            if (!empty($data['search'])) {
                $userSubscriptions->where(function ($query) use ($data) {
                    $query->where('email', 'like', '%' . $data['search'] . '%')
                        ->orWhere('name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('username', 'like', '%' . $data['search'] . '%');
                });
            }

            $totalFiltered = $userSubscriptions->count();
        }

        $userSubscriptions = $userSubscriptions
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        foreach ($userSubscriptions as $key => $userSubscription) {
            $userSubscription->sno  = $key + 1;
            $userSubscription->actions = '<ul class="action align-center">
                                <li class="edit">
                                    <a href="' . route('admin.subscription.edit.data', ['subscription' => $userSubscription->id]) . '" data-toggle="tooltip" data-placement="top" title="Edit">
                                     <i class="icon-pencil-alt"></i>
                                    </a>
                                </li>
                            </ul>';

            $userSubscription->user;
            $userSubscription->subscriptionPaymentMethod?->paymentMethod;
            $userSubscription->subscriptionPlan?->plan;
            $userSubscription->subscriptionType;
            $userSubscription->subscription;
            $userSubscription->amount = '$' . number_format($userSubscription->amount, 2);

            // <li class="activate">
            //     <a href="javascript:void(0);" class="btn-activate" data-id="'.$user->id.'" data-action="'.($user->is_active ? 'deactivate' : 'activate').'" data-toggle="tooltip" data-placement="top" title="'.($user->is_active ? 'Deactivate' : 'Activate').' User" >
            //         '.($user->is_active ? "<i class='icon-reload text-danger'></i>" : "<i class='icon-reload text-success'></i>").'
            //     </a>
            // </li>
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = $userSubscriptions;
        return response()->json($response, 200);
    }

    public function editSubscription(UserSubscriptions $subscription)
    {
        $subscriptions = $this->subscriptions->get();
        $subscriptionTypes = $this->subscriptionTypes->with('subscription')
            ->where('subscription_id', $subscription->subscription_id)->get();
        $subscriptionPlans = $this->subscriptionPlans->with('subscriptionType')->where('subscription_type_id', $subscription->subscription_type_id)->get();
        $paymentMethods = $this->subscriptionPaymentMethods
            ->with('paymentMethod')
            ->where('subscription_id', $subscription->subscription_id)->get();

        return view('subscription.edit', compact('subscription', 'subscriptions', 'paymentMethods', 'subscriptionPlans', 'subscriptionTypes'));
    }

    public function updateSubscription(UserSubscriptions $subscription, UpdateRequest $request)
    {
        try {
            $subscriptionPlan = $this->subscriptionPlans->find($request->subscription_plan_id);

            $subscription->user_id   =  $request->user_id;
            $subscription->subscription_id   =  $request->subscription_id;
            $subscription->subscription_type_id   =  $request->subscription_type_id;
            $subscription->subscription_plan_id   = $request->subscription_plan_id;
            $subscription->subscription_payment_id   =  $request->subscription_payment_id;
            $subscription->amount    =  $subscriptionPlan->amount;
            $subscription->type    =  strtolower(str_replace(' ', '-', $subscriptionPlan->type));

            // Set start and end dates
            $startDate = Carbon::now();
            $endDate = null;

            switch ($subscription->type) {
                case 'yearly':
                    $endDate = $startDate->copy()->addYear();
                    break;
                case 'monthly':
                    $endDate = $startDate->copy()->addMonth();
                    break;
                case 'lifetime':
                    $endDate = null; // No end date for lifetime subscriptions
                    break;
            }

            $subscription->status  =  ($request->payment_status == 'paid') ? 'active' : 'inactive';
            $subscription->payment_status    = $request->payment_status;

            $subscription->start_date = ($subscription->status == 'active') ? date('Y-m-d', strtotime($startDate)) : null;
            if ($endDate) {
                $subscription->end_date = ($subscription->status == 'active') ? date('Y-m-d', strtotime($endDate)) : null;
            } else {
                $subscription->end_date = null; // No end date for lifetime subscriptions
            }

            if ($subscription->save()) {
                return redirect()->route('admin.subscription.list')->with('success', 'Subscription details updated successfully.');
            }

            return redirect()->back()->with('failed', 'Subscription details not updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
