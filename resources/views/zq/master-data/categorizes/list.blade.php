@extends('layouts.simple.master')

@section('title', 'Categorizes List')

@section('css')

@endsection

@section('style')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">

@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>Categorizes List</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Master data</li>
    <li class="breadcrumb-item active">Categorizes List</li>
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
                            <input type="search" class="form-control" placeholder="Categorizes" id="search" />
                        </div>

                        <div class="col-1">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary pt-2 pb-2 fs-5">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <div class="col-2">
                            <a href="{{ route('zq.categorizes.create') }}" class="btn btn-primary">Add Categorize</a>
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
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>On Menu</th>
                                        <th>On Frontend</th>
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

    <input type="hidden" value="{{route('zq.categorizes.fetch.data.ajax')}}" id="categorizeFetchListDataUrl" />
    <input type="hidden" value="{{ route('zq.categorizes.update.status', ['categorizeId' => ':categorizeId', 'action' => ':action']) }}" id="updateStatusUrl" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="{{ asset('assets/src/js/master-data/categorizes/script.js') }}"></script>
@endsection
