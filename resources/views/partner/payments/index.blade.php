@extends('layouts.simple.master')

@section('title', 'Transaction')

@section('css')

@endsection

@section('style')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Transactions</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">

            <div class="row page_detail_summary">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card mini-card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{'$'.$yearEarningAmount}}</h4><span class="f-light">Total Earning Last 12 Months</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card mini-card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{'$'.$monthEarningAmount}}</h4><span class="f-light">Total Earnings This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card mini-card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$monthTransactions->count()}}</h4><span class="f-light">Transactions This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card custom_card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-1">
                            <select class="form-control" id="customPageLength" title="per page">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <input type="text" class="form-control" placeholder="User" id="username" />
                        </div>

                        <div class="col-3">
                            <input type="text" class="form-control" placeholder="Payment Ref" id="paymentRef" />
                        </div>

                        {{-- <div class="col-2">
                            <select class="form-control" id="country_id" name="country_id">
                                <option value="">Select Territory</option>
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-2">
                            <select class="form-control" id="records_duration">
                                <option value="all_time" selected>All Time</option>
                                <option value="current_year">This Year</option>
                                <option value="current_month" >This Month</option>
                                <option value="current_week">This Week</option>
                                <option value="today">Today</option>
                            </select>
                        </div>

                        {{-- <div class="col-2">
                            <select class="form-control" id="territories" name="country_id">
                            <option>Territory</option>
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-1">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary float-right pt-2 pb-2 fs-5">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom_card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="display" id="fetchDataToTable">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Created On</th>
                                        <th>User</th>
                                        <th>Payment</th>
                                        <th>Payment Ref</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
  </div>
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>

    <input type="hidden" value="{{route('partner.payments.fetch.data.ajax', ['type'=>'partner', 'request_data' => request()->request_data ?? 'yearly'])}}" id="fetchDataToTableUrl" />
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/payment/script.js') }}"></script>
@endsection
