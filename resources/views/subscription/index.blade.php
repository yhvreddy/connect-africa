@extends('layouts.simple.master')

@section('title', 'Subscriptions List')

@section('css')

@endsection

@section('style')
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Subscription List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{ app('truFlix')->getSessionUser()->role->title }}</li>
    <li class="breadcrumb-item active">Subscription List</li>
@endsection

@section('content')
    <div class="container-fluid">
        @include('layouts.alerts')

        <div class="row widget-grid">
            <div class="col-sm-12">
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

                            <div class="col-8">
                                <input type="search" class="form-control" placeholder="Search Users Here.."
                                    id="search" />
                            </div>

                            {{-- <div class="col-2">
                            <select class="form-control" id="user_status">
                                <option value="" selected>All Users</option>
                                <option value="active">Active Users</option>
                                <option value="deactivated">Deactivated Users</option>
                            </select>
                        </div> --}}

                            <div class="col-1">
                                <a href="javascript:void(0)" id="searchFilterButton"
                                    class="btn btn-primary float-right pt-2 pb-2 fs-5">
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>subscription</th>
                                            <th>Plan</th>
                                            <th>Amount</th>
                                            <th>status</th>
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

    <input type="hidden" value="{{ route('admin.subscription.fetch.data.list') }}" id="fetchListDataUrl" />
    <input type="hidden" value="{{ route('admin.update.status', ['userId' => ':userId', 'action' => ':action']) }}"
        id="updateStatusUrl" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/subscriptions/script.js') }}"></script>
@endsection
