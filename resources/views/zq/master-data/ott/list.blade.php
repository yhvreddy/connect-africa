@extends('layouts.simple.master')

@section('title', 'OTT List')

@section('css')
    
@endsection

@section('style')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">

@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>OTT List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">OTT</li>
    <li class="breadcrumb-item active">OTT List</li>
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

                        <div class="col-4">
                            <input type="search" class="form-control" placeholder="OTT" id="search" />
                        </div>

                        <div class="col-1">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary float-right pt-2 pb-2 fs-5">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('zq.ott.create') }}" class="btn btn-primary float-end">Add OTT</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="display" id="ottFetchDataToTable">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Image</th>
                                        <th>Title</th>
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

    <input type="hidden" value="{{route('zq.otts.fetch.data.ajax')}}" id="ottFetchListDataUrl" />
    <input type="hidden" value="{{ route('zq.otts.update.status', ['ottId' => ':ottId', 'action' => ':action']) }}" id="updateStatusUrl" />

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/master-data/ott/script.js') }}"></script>

@endsection