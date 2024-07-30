@extends('layouts.simple.master')

@section('title', 'Users List')

@section('css')

@endsection

@section('style')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
@endsection

@section('breadcrumb-title')
    <h3>Users List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Users List</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">

            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card mini-card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$users->count()}}</h4><span class="f-light">Total Users</span>
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
                                    <h4>{{$currentMonthRecords->count()}}</h4><span class="f-light">New This Month</span>
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
                                    <h4>{{$deactivatedUsers->count()}}</h4><span class="f-light">Deactivated This Month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="card">
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
                        <div class="col-4">
                            <input type="search" class="form-control" placeholder="User" id="search" />
                        </div>
                        <div class="col-3">
                            <select class="form-control" id="records_duration">
                                <option value="all_time" selected>All Time</option>
                                <option value="current_month">This Month</option>
                                <option value="current_week">This Week</option>
                                <option value="current_year">This Year</option>
                            </select>
                        </div>

                        <div class="col-3">
                            <select class="form-control" id="user_status">
                                <option value="" selected>All Users</option>
                                <option value="1">Active Users</option>
                                <option value="2">Failed Users</option>
                                <option value="0">Deactivated Users</option>
                            </select>
                        </div>

                        <div class="col-1">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary float-right pt-1 pb-1 fs-5">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="display" id="fetchDataToTable">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Email</th>
                                        <th>Name</th>
                                        <th>Joined</th>
                                        <th>Referred</th>
                                        <th>Status</th>
                                        <th>Access Code</th>
                                        <th class="text-center">Actions</th>
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

    <input type="hidden" value="{{route('partner.territory.users.fetch.data.ajax', ['request_data' => request()->request_data ?? 'all_users'])}}" id="fetchListDataUrl" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/territory/users_script.js') }}"></script>
@endsection
