@extends('layouts.simple.master')

@section('title', 'Affiliate List')

@section('css')

@endsection

@section('style')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Affiliate List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Affiliates</li>
    <li class="breadcrumb-item active">Affiliate List</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$affiliates->count()}}</h4><span class="f-light">Total Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$newRecords->count()}}</h4><span class="f-light">New This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$deactivatedRecords->count()}}</h4><span class="f-light">Deactivated This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-1 mb-2">
                            <select class="form-control" id="customPageLength" title="per page">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="col-3 mb-2">
                            <input type="search" class="form-control" placeholder="Affiliate" id="search" />
                        </div>

                        <div class="col-2 mb-2">
                            <select class="form-control" id="records_duration">
                                <option value="all_time" selected>All Time</option>
                                <option value="current_year">This Year</option>
                                <option value="current_month">This Month</option>
                                <option value="current_week">This Week</option>
                                <option value="today">Today</option>
                            </select>
                        </div>

                        <div class="col-2 mb-2">
                            <select class="form-control" id="user_status">
                                <option value="active">Activate</option>
                                <option value="deactivated" selected>Deactivate</option>
                            </select>
                        </div>

                        <div class="col-2 mb-2">
                            <select class="form-control" id="country_id">
                                <option value="" selected>All Territories</option>
                                @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-2 mb-2">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary btn-sm pt-2 pb-2 fs-5">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-striped" id="fetchDataToTable">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Joined</th>
                                    <th>Referred</th>
                                    <th>Territory</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Access Code</th>
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
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>

    @if(isset($user))
        <input type="hidden" value="{{route('default.affiliates.fetch.data.ajax', ['id' => $user->id])}}" id="fetchListDataUrl" />
    @else
        <input type="hidden" value="{{route('zq.affiliates.fetch.data.ajax')}}" id="fetchListDataUrl" />
        <input type="hidden" value="{{ route('zq.affiliates.update.status', ['affiliateId' => ':affiliateId', 'action' => ':action']) }}" id="updateStatusUrl" />
    @endif

    <input type="hidden" value="deactivated" id="user_status" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/affiliates/script.js') }}"></script>
@endsection
