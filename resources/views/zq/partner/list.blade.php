@extends('layouts.simple.master')

@section('title', 'Partner List')

@section('css')
    
@endsection

@section('style')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">

@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Partner List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Partners</li>
    <li class="breadcrumb-item active">Partner List</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

  <div class="row widget-grid">
        <div class="col-sm-12">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"> 
                    <div class="card">
                        <div class="card-body"> 
                            <div class="widget-content">
                                <div> 
                                    <h4>{{$partners->count()}}</h4><span class="f-light">Total Partners</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"> 
                    <div class="card">
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
                    <div class="card">
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
                        <div class="col-1">
                            <select class="form-control" id="customPageLength" title="per page">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="search" class="form-control" placeholder="Partner" id="search" />
                        </div>

                        <div class="col-3">
                            <select class="form-control" id="records_duration">
                                <option value="all_time" selected>All Time</option>
                                <option value="current_year">This Year</option>
                                <option value="current_month">This Month</option>
                                <option value="current_week">This Week</option> 
                                <option value="today">Today</option>
                            </select>
                        </div>

                        <div class="col-2">
                            <select class="form-control" id="user_status">
                                <option value="active" selected>Active</option>
                                <option value="deactivated">Deactivated</option>
                            </select>
                        </div>

                        <div class="col-2">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary pt-2 pb-2 fs-5">
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
                            <table class="display" id="partnerFetchDataToTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Territory</th>
                                        <th>Partner</th>
                                        <th>Email / Username</th>
                                        <th>Active User</th>
                                        <th class="text-center">Action</th>
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

    <input type="hidden" value="{{route('zq.partner.fetch.data.ajax')}}" id="partnerFetchListDataUrl" />
    <input type="hidden" value="{{ route('zq.partner.update.status', ['partnerId' => ':partnerId', 'action' => ':action']) }}" id="updateStatusUrl" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/partners/script.js') }}"></script>
@endsection