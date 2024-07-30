@extends('layouts.simple.master')

@section('title', 'Movies List')

@section('css')

@endsection

@section('style')
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">

@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('breadcrumb-title')
    <h3>{{$categorize?->title}}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Movies</li>
    <li class="breadcrumb-item active">{{$categorize->title}}</li>
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
                                    <h4>{{$movies->count()}}</h4><span class="f-light">Total Movies</span>
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
                                    <h4>{{$newRecordsThisWeek->count()}}</h4><span class="f-light">New This Week</span>
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
                            <input type="search" class="form-control" placeholder="Movies" id="search" />
                        </div>

                        <div class="col-2">
                            <select class="form-control" id="records_duration">
                                <option value="all_time">All Time</option>
                                <option value="current_year">This Year</option>
                                <option value="current_month">This Month</option>
                                <option value="current_week">This Week</option>
                                <option value="today">Today</option>
                            </select>
                        </div>

                        @if(isset(app('truFlix')->getSessionUser()->role->slug) && app('truFlix')->getSessionUser()->role->slug === 'zq')
                            <div class="col-3">
                                <select class="form-control" id="admin_id">
                                    <option value="">All Admins</option>
                                    @foreach ($admins as $admin)
                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-1">
                            <a href="javascript:void(0)" id="searchFilterButton" class="btn btn-primary float-right pt-2 pb-2 fs-5">
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
                                        <th>Poster Image</th>
                                        <th>Title</th>
                                        <th>Year</th>
                                        {{-- <th>Rated</th>
                                        <th>Imdb Score</th> --}}
                                        <th>Dated Added</th>
                                        <th>Order</th>
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
    <input type="hidden" value="{{route('default.fetch.data.ajax')}}" id="fetchListDataUrl" />

    @if(isset(app('truFlix')->getSessionUser()->role->slug) && app('truFlix')->getSessionUser()->role->slug === 'zq')
        <input type="hidden" value="false" id="isRequestAdmin" />
    @else
        <input type="hidden" value="{{ route('default.movie.update.status', ['movieId' => ':movieId', 'action' => ':action']) }}" id="updateStatusUrl" />
        <input type="hidden" value="true" id="isRequestAdmin" />
    @endif

    <input type="hidden" value="{{$categorize->id ?? null}}" id="isCategorizeList" />
    <input type="hidden" value="{{route('default.entertainment.order.update', ['type'=>'movies'])}}" id="typeOfUpdateOrderUrl" />

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    @if(isset(app('truFlix')->getSessionUser()->role->slug) && app('truFlix')->getSessionUser()->role->slug === 'zq')
        <script src="{{ asset('assets/src/js/movies/zq_script.js') }}"></script>
    @else
        <script src="{{ asset('assets/src/js/movies/script.js') }}"></script>
    @endif
@endsection
