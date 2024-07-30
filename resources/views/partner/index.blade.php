@extends('layouts.simple.master')

@section('title', 'Partner Dashboard')

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
	<div class="container-fluid">

		<div class="card" style="background-color:#34FF94;color: #000000">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h5>{{$user->country->name}}</h5>
						<span>Partner Dashboard</span>
					</div>
				</div>
			</div>
		</div>

		<div class="row widget-grid">
			<div class="mb-2">
				<h4>Territory Overview</h4>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<div class="card">
					<div class="card-body">
						<div class="widget-content">
							<div>
								<h4>{{date('M d, Y', strtotime($user->created_at))}}</h4><span class="f-light">Start Date</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<a href="{{route('partner.territory.users')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$totalUsers->count()}}</h4><span class="f-light">Total Users</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<a href="{{route('partner.territory.users', ['request_data'=>'new_users'])}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$newUsers->count()}}</h4><span class="f-light">New Users</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<a href="{{route('partner.affiliate')}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$affiliateUsers->count()}}</h4><span class="f-light">Affilates</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<a href="{{route('partner.territory.users', ['request_data'=>'deactivated_users'])}}">
					<div class="card">
						<div class="card-body">
							<div class="widget-content">
								<div>
									<h4>{{$totalDeactivatedUsers->count()}}</h4><span class="f-light">Deactivated</span>
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>


			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<a href="{{route('partner.payments.index', ['request_data'=>'monthly'])}}">
				<div class="card">
					<div class="card-body">
						<div class="widget-content">
							<div>
								<h4>{{'$'.$totalEarningsOfMonthAmount}}</h4><span class="f-light">Month Earnings</span>
							</div>
						</div>
					</div>
				</div>
				</a>
			</div>

		</div>
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
