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

                                @include('subscription.user_subscription')

                            </div>
                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary mt-4">Submit</button>
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
    @include('subscription.script')
@endsection
