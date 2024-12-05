<div class="col-md-12 mt-3">
    <h5>User Subscription</h5>
    <div class="row">
        <!-- Subscriptions Dropdown -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="subscription" class="mb-1">Select Subscription</label>
                <select id="subscription" name="subscription_id" class="form-control" required>
                    <option value="">Select Subscription</option>
                    @foreach ($subscriptions as $sSubscription)
                        <option value="{{ $sSubscription->id }}"
                            {{ $subscription?->subscription_id == $sSubscription->id ? 'selected' : '' }}>
                            {{ $sSubscription->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Subscription Types Dropdown -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="subscription_type" class="mb-1">Subscription Type</label>
                <select id="subscription_type" name="subscription_type_id" class="form-control" required>
                    <option value="">Select Type</option>
                    @foreach ($subscriptionTypes as $subscriptionType)
                        <option value="{{ $subscriptionType->id }}"
                            {{ $subscription?->subscription_type_id == $subscriptionType->id ? 'selected' : '' }}>
                            {{ $subscriptionType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Subscription Plans Dropdown -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="subscription_plan" class="mb-1">Subscription Plan</label>
                <select id="subscription_plan" name="subscription_plan_id" class="form-control" required>
                    <option value="">Select Plan</option>
                    @foreach ($subscriptionPlans as $subscriptionPlan)
                        <option value="{{ $subscriptionPlan->id }}"
                            {{ $subscription?->subscription_plan_id == $subscriptionPlan->id ? 'selected' : '' }}>
                            {{ $subscriptionPlan->name . ' - ' . '$' . number_format($subscriptionPlan->amount, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Subscription Payment Methods Dropdown -->
        <div class="col-md-3">
            <div class="form-group">
                <label for="subscription_payment_method" class="mb-1">Payment
                    Method</label>
                <select id="subscription_payment_method" name="subscription_payment_id" class="form-control" required>
                    <option value="">Select Payment Method</option>
                    @foreach ($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->id }}"
                            {{ $subscription?->subscription_payment_id == $paymentMethod->id ? 'selected' : '' }}>
                            {{ $paymentMethod->paymentMethod->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-md-3 mt-2">
            <div class="form-group">
                <label for="payment_status" class="mb-1">Payment Status</label>
                <select id="payment_status" name="payment_status" class="form-control" required>
                    <option value="">Select Payment Status</option>
                    <option {{ $subscription?->payment_status == 'paid' ? 'selected' : '' }} value="paid">Paid
                    </option>
                    <option {{ $subscription?->payment_status == 'unpaid' ? 'selected' : '' }} value="unpaid">Unpaid
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>
