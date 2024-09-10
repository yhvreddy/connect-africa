<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Subscription;
use App\Models\SubscriptionTypes;
use App\Models\SubscriptionPlans;
use App\Models\PaymentMethods;
use App\Models\SubscriptionPaymentMethod;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Subscriptions
        $individual = Subscription::create([
            'name' => 'Individual'
        ]);

        $corporate = Subscription::create([
            'name' => 'Corporate'
        ]);

        $government = Subscription::create([
            'name' => 'Government'
        ]);

        //payments
        $wallet = PaymentMethods::create([
            'name'  =>  'eWallet',
            'slug'  =>  'e-wallet'
        ]);

        $ptc = PaymentMethods::create([
            'name'  =>  'Pay To Cell',
            'slug'  =>  'ptc'
        ]);

        $cash = PaymentMethods::create([
            'name'  =>  'Cash',
            'slug'  =>  'cash'
        ]);

        $eft = PaymentMethods::create([
            'name'  =>  'EFT',
            'slug'  =>  'eft'
        ]);

        //subscription payment methods
        $individualPayments = [
            [
                'subscription_id' => $individual->id,
                'payment_method_id' =>  $wallet->id,
            ],
            [
                'subscription_id' => $individual->id,
                'payment_method_id' =>  $ptc->id,
            ],
            [
                'subscription_id' => $individual->id,
                'payment_method_id' =>  $cash->id,
            ],
            [
                'subscription_id' => $individual->id,
                'payment_method_id' =>  $eft->id,
            ]
        ];
        foreach ($individualPayments as $key => $individualPayment) {
            SubscriptionPaymentMethod::create($individualPayment);
        }

        $corporatePayments = [
            [
                'subscription_id' => $corporate->id,
                'payment_method_id' =>  $eft->id,
            ]
        ];
        foreach ($corporatePayments as $key => $corporatePayment) {
            SubscriptionPaymentMethod::create($corporatePayment);
        }

        $governmentPayments = [
            [
                'subscription_id' => $government->id,
                'payment_method_id' =>  $eft->id,
            ]
        ];
        foreach ($governmentPayments as $key => $governmentPayment) {
            SubscriptionPaymentMethod::create($governmentPayment);
        }

        //subscription types
        $individualTypes = [
            [
                'subscription_id'   =>  $individual->id,
                'name'  =>  'Home'
            ],
            [
                'subscription_id'   =>  $individual->id,
                'name'  =>  'Student'
            ],
            [
                'subscription_id'   =>  $individual->id,
                'name'  =>  'Professional'
            ]
        ];
        foreach ($individualTypes as $key => $individualType) {
            SubscriptionTypes::create($individualType);
        }

        $corporateTypes = [
            [
                'subscription_id'   =>  $corporate->id,
                'name'  =>  '20'
            ],
            [
                'subscription_id'   =>  $corporate->id,
                'name'  =>  '50'
            ],
            [
                'subscription_id'   =>  $corporate->id,
                'name'  =>  '100'
            ]
        ];
        foreach ($corporateTypes as $key => $corporateType) {
            SubscriptionTypes::create($corporateType);
        }

        $governmentTypes = [
            [
                'subscription_id'   =>  $government->id,
                'name'  =>  '20'
            ],
            [
                'subscription_id'   =>  $government->id,
                'name'  =>  '50'
            ],
            [
                'subscription_id'   =>  $government->id,
                'name'  =>  '100'
            ]
        ];
        foreach ($governmentTypes as $key => $governmentType) {
            SubscriptionTypes::create($governmentType);
        }


        //subscription plans
        $individualSubscriptionTypes = SubscriptionTypes::where('subscription_id', $individual->id)->get();
        if (count($individualSubscriptionTypes) > 0) {
            $individualHomePlans = [
                [
                    'name' =>  'Monthly',
                    'amount' => 50,
                    'type' => 'monthly'
                ],
                [
                    'name' =>  'Annually',
                    'amount' => 500,
                    'type' => 'yearly'
                ],
                [
                    'name' =>  'Lifetime',
                    'amount' => 5555,
                    'type' => 'lifetime'
                ]
            ];
            foreach ($individualSubscriptionTypes as $individualSubscriptionType) {
                foreach ($individualHomePlans as $individualHomePlan) {
                    $individualHomePlan['subscription_type_id'] =   $individualSubscriptionType->id;
                    SubscriptionPlans::create($individualHomePlan);
                }
            }
        }

        $corporateSubscriptionTypes = SubscriptionTypes::where('subscription_id', $corporate->id)->get();
        if (count($corporateSubscriptionTypes) > 0) {
            $corporatePlans = [
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 5000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 55000,
                        'type' => 'annually'
                    ]
                ],
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 10000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 110000,
                        'type' => 'annually'
                    ]
                ],
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 20000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 220000,
                        'type' => 'annually'
                    ]
                ]
            ];
            foreach ($corporateSubscriptionTypes as $key => $corporateSubscriptionType) {
                foreach ($corporatePlans[$key] as $corporatePlan) {
                    $corporatePlan['subscription_type_id'] =   $corporateSubscriptionType->id;
                    SubscriptionPlans::create($corporatePlan);
                }
            }
        }


        $governmentSubscriptionTypes = SubscriptionTypes::where('subscription_id', $government->id)->get();
        if (count($governmentSubscriptionTypes) > 0) {
            $governmentPlans = [
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 4000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 44000,
                        'type' => 'annually'
                    ]
                ],
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 10000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 110000,
                        'type' => 'annually'
                    ]
                ],
                [
                    [
                        'name' =>  'Monthly',
                        'amount' => 20000,
                        'type' => 'monthly'
                    ],
                    [
                        'name' =>  'Annually',
                        'amount' => 220000,
                        'type' => 'annually'
                    ]
                ]
            ];
            foreach ($governmentSubscriptionTypes as $key => $governmentSubscriptionType) {
                foreach ($governmentPlans[$key] as $governmentPlan) {
                    $governmentPlan['subscription_type_id'] =   $governmentSubscriptionType->id;
                    SubscriptionPlans::create($governmentPlan);
                }
            }
        }
    }
}
