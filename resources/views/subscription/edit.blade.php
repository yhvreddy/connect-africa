@extends('layouts.simple.master')

@section('title', 'Subscriptions Edit')

@section('css')

@endsection

@section('style')
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Edit Subscription</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{ app('truFlix')->getSessionUser()->role->title }}</li>
    <li class="breadcrumb-item">Subscriptions</li>
    <li class="breadcrumb-item active">Edit Subscription</li>
@endsection

@section('content')
    <div class="container-fluid">
        @include('layouts.alerts')

        <div class="row widget-grid">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.subscription.update.data', ['subscription' => $subscription->id]) }}"
                            method="POST">
                            @csrf

                            <input type="hidden" name="user_id" value="{{ $subscription->user_id }}" />

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Subscription Details:</h5>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Name : </label> {{ $subscription->user?->name }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Email : </label> {{ $subscription->user?->email }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Mobile : </label> {{ $subscription->user?->mobile }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Subscription : </label> {{ $subscription->subscription?->name }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Subscription Type : </label> {{ $subscription->subscriptionType?->name }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Subscription Plan : </label> {{ $subscription->subscriptionPlan?->name }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Price : </label> {{ number_format($subscription->amount, 2) }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Payment Status : </label> {{ $subscription->payment_status }}
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label>Subscription Status: </label>
                                    {{ in_array($subscription->payment_status, ['pending', 'unpaid']) ? 'Inactive' : 'Active' }}
                                </div>

                                <div class="col-md-12 mt-3">
                                    <h5>User Subscription</h5>
                                    <div class="row">
                                        <!-- Subscriptions Dropdown -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subscription" class="mb-1">Select Subscription</label>
                                                <select id="subscription" name="subscription_id" class="form-control"
                                                    required>
                                                    <option value="">Select Subscription</option>
                                                    @foreach ($subscriptions as $sSubscription)
                                                        <option value="{{ $sSubscription->id }}"
                                                            {{ $subscription->subscription_id == $sSubscription->id ? 'selected' : '' }}>
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
                                                <select id="subscription_type" name="subscription_type_id"
                                                    class="form-control" required>
                                                    <option value="">Select Type</option>
                                                    @foreach ($subscriptionTypes as $subscriptionType)
                                                        <option value="{{ $subscriptionType->id }}"
                                                            {{ $subscription->subscription_type_id == $subscriptionType->id ? 'selected' : '' }}>
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
                                                <select id="subscription_plan" name="subscription_plan_id"
                                                    class="form-control" required>
                                                    <option value="">Select Plan</option>
                                                    @foreach ($subscriptionPlans as $subscriptionPlan)
                                                        <option value="{{ $subscriptionPlan->id }}"
                                                            {{ $subscription->subscription_plan_id == $subscriptionPlan->id ? 'selected' : '' }}>
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
                                                <select id="subscription_payment_method" name="subscription_payment_id"
                                                    class="form-control" required>
                                                    <option value="">Select Payment Method</option>
                                                    @foreach ($paymentMethods as $paymentMethod)
                                                        <option value="{{ $paymentMethod->id }}"
                                                            {{ $subscription->subscription_payment_id == $paymentMethod->id ? 'selected' : '' }}>
                                                            {{ $paymentMethod->paymentMethod->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label for="payment_status" class="mb-1">Payment Status</label>
                                                <select id="payment_status" name="payment_status" class="form-control"
                                                    required>
                                                    <option value="">Select Payment Status</option>
                                                    <option {{ $subscription->payment_status == 'paid' ? 'selected' : '' }}
                                                        value="paid">Paid</option>
                                                    <option
                                                        {{ $subscription->payment_status == 'unpaid' ? 'selected' : '' }}
                                                        value="unpaid">Unpaid</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary mt-4">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- AJAX Script to Load Child Dropdowns -->
    <script>
        let selectedSubscriptionId = "{{ $subscription->subscription_id ?? null }}";
        let selectedSubscriptionTypeId = "{{ $subscription->subscription_type_id ?? null }}";
        let selectedSubscriptionPlanId = "{{ $subscription->subscription_plan_id ?? null }}";
        let selectedSubscriptionPaymentId = "{{ $subscription->subscription_payment_id ?? null }}";

        $(document).ready(function() {
            // Load Subscription Types based on Subscription selection
            $('#subscription').change(function() {
                let subscription_id = $(this).val();
                getSubscriptionTypes(subscription_id);
            });

            // Load Subscription Plans based on Subscription Type selection
            $('#subscription_type').change(function() {
                let subscription_type_id = $(this).val();
                getSubscriptionPlans(subscription_type_id);
            });
        });

        function getSubscriptionTypes(subscription_id) {
            if (subscription_id) {
                $.ajax({
                    url: "{{ route('get.subscription.types') }}",
                    type: "GET",
                    data: {
                        subscription: subscription_id
                    },
                    success: function(response) {
                        console.log(response.data);
                        $('#subscription_type').html(
                            '<option value="">Select Type</option>');
                        $.each(response.data, function(key, value) {

                            $('#subscription_type').append('<option value="' + value.id + '">' + value
                                .name + '</option>');

                        });

                        getPaymentMethods(subscription_id);
                    }
                });
            } else {
                $('#subscription_type').html('<option value="">Select Type</option>');
                $('#subscription_plan').html('<option value="">Select Plan</option>');
                $('#subscription_payment_method').html(
                    '<option value="">Select Payment Method</option>');
            }
        }

        function getSubscriptionPlans(subscription_type_id) {
            if (subscription_type_id) {
                $.ajax({
                    url: "{{ route('get.subscription.plans') }}",
                    type: "GET",
                    data: {
                        subscriptionTypeId: subscription_type_id
                    },
                    success: function(response) {
                        $('#subscription_plan').html(
                            '<option value="">Select Plan</option>');
                        $.each(response.data, function(key, value) {
                            $('#subscription_plan').append('<option value="' + value
                                .id +
                                '" ((selectedSubscriptionTypeId == value.id)?"selected":"")  >' +
                                value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#subscription_plan').html('<option value="">Select Plan</option>');
                $('#subscription_payment_method').html(
                    '<option value="">Select Payment Method</option>');
            }
        }

        function getPaymentMethods(subscription_id) {
            if (subscription_id) {
                $.ajax({
                    url: "{{ route('get.subscription.payment.methods') }}",
                    type: "GET",
                    data: {
                        subscription: subscription_id
                    },
                    success: function(response) {
                        $('#subscription_payment_method').html(
                            '<option value="">Select Payment Method</option>');
                        $.each(response.data, function(key, value) {
                            $('#subscription_payment_method').append(
                                '<option value="' + value.id + '">' + value
                                .payment_method.name + '</option>');
                        });
                    }
                });
            } else {
                $('#subscription_payment_method').html(
                    '<option value="">Select Payment Method</option>');
            }
        }
    </script>
@endsection
