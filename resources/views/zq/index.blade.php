@extends('layouts.simple.master')

@section('title', 'ZQ Dashboard')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>{{app('truFlix')->getSessionUser()->role->title}} Dashboard</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
	<div class="container-fluid login_dashboard">
		@include('layouts.alerts')

		<div class="row">
			<h4>Company Overview</h4>

			<div class="col-xs-12 col-sm-12 col-3 col-lg-3">
				<a href="{{route('zq.partners.index')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$partners}}</h4><span class="f-light">Partners</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-3 col-lg-3">
				<a href="{{route('zq.affiliates.index')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$affiliates}}</h4><span class="f-light">Affliates</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-3 col-lg-3">
				<a href="{{route('zq.users.index')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$users}}</h4><span class="f-light">Users</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-3 col-lg-3">
				<a href="{{route('zq.admins.index')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$deactivatedUsers}}</h4><span class="f-light">Deactivated Users</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-3 col-lg-3">
				<a href="{{route('zq.admins.index')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$admins}}</h4><span class="f-light">Admins</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>

		<div class="row">
			<h4>Earning Overview</h4>

			<div class="col-12">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<a>
							<div class="card">
								<div class="card-body">
									<div class="widget-content">
										<div>
											<h4>{{'$'.$totalEarningsOfYearAmount}}</h4><span class="f-light">Total Earnings This Year</span>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<a>
							<div class="card">
								<div class="card-body">
									<div class="widget-content">
										<div>
											<h4>{{'$'.$totalEarningsOfMonthAmount}}</h4><span class="f-light">Total Earnings This Month</span>
										</div>
									</div>
								</div>
							</div>
						</a>
					</div>
				</div>
			</div>

		</div>

		@if(count($countries) > 0)
			<div class="row">
				<h4>Territory Overview</h4>

				@foreach ($countries as $key => $country)

					@php
						$earningThisMount = app('\App\Repositories\UserSubscriptionsRepository')
											->leftJoin('users', 'users.id', 'users_subscriptions.user_id')
											->select('users_subscriptions.response_data', 'users_subscriptions.total_amount')
											->where('users.country_id', $country->id)
											->where('users_subscriptions.subscription_status', 'active')
											->get();

						$earningMountOfMount = 0;
						foreach ($earningThisMount as $key => $value) {
							$amount = $value->total_amount ?? ((json_decode($value->response_data)->amount_paid ?? 0) / 100);
            				$earningMountOfMount += number_format($amount, 2);
						}

						$totalUsers = app('\App\Repositories\UserRepository')->where('country_id', $country->id)
										->where('role_id', 4)->get()->count();

						$newUsers = app('\App\Repositories\UserRepository')->where('country_id', $country->id)
										->where('role_id', 4)
										->whereBetween('created_at', [\Illuminate\Support\Carbon::now()->startOfWeek(), \Illuminate\Support\Carbon::now()->endOfWeek()])->get()->count();
					@endphp


					<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 territory_overview">
						<div class="card territory_overview_card">
							<div class="card-header">
								<h6>{{$country->name}}</h6>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-4">
										<div class="row">
											<div class="h6">Earnings This Month</div>
											<div class="h5">{{'$'.$earningMountOfMount}}</div>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="row">
											<div class="h6">Total Users</div>
											<div class="h5">{{$totalUsers}}</div>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="row">
											<div class="h6">New Users</div>
											<div class="h5">{{$newUsers}}</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
				@endforeach

			</div>
		@endif
	</div>
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>
@endsection

@section('script')
<script src="{{ asset('assets/js/clock.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/dashboard/default.js') }}"></script>
<script src="{{ asset('assets/js/notify/index.js') }}"></script>
<script src="{{ asset('assets/js/typeahead/handlebars.js') }}"></script>
<script src="{{ asset('assets/js/typeahead/typeahead.bundle.js') }}"></script>
<script src="{{ asset('assets/js/typeahead/typeahead.custom.js') }}"></script>
<script src="{{ asset('assets/js/typeahead-search/handlebars.js') }}"></script>
<script src="{{ asset('assets/js/typeahead-search/typeahead-custom.js') }}"></script>
<script src="{{ asset('assets/js/height-equal.js') }}"></script>
<script src="{{ asset('assets/js/animation/wow/wow.min.js') }}"></script>
@endsection
