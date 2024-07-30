@extends('layouts.simple.master')

@section('title', 'Territory Overview')

@section('css')

@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <h3>{{$user->country->name ?? 'Territory'}}  Overview</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Territory</li>
    <li class="breadcrumb-item active">Territory Overview</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">

            <div class="card mini-card">
                <div class="card-body">
                    <div class="row">
                       <div class="col-md-12">
                            <h5>{{$territory->country->name}}</h5>
                            <span>Territory Overview</span>
                       </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="javascript:0;">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{date('M d, Y', strtotime($territory->created_at))}}</h4><span class="f-light">Date Added</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="{{route('default.show.users', ['type'=>'all-users', 'affiliate' => $territory->id])}}">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{$territoryUsers->count()}}</h4><span class="f-light">Total Users</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$territory->country->code}}</h4><span class="f-light">User Cap</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="{{route('default.affiliates.index', ['id' => $territory->id])}}">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{$affiliates->count()}}</h4><span class="f-light">Affiliates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{$newRecords->count()}}</h4><span class="f-light">New</span>
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
                                    <h4>{{$deactivated->count()}}</h4><span class="f-light">Deactivated</span>
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
                                    <h4>{{'$'.($monthEarningAmount ?? 0)}}</h4><span class="f-light">Month Earnings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>0</h4><span class="f-light">Release Date</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
	</div>
  </div>
    <script type="text/javascript">
        var session_layout = '{{ session()->get('layout') }}';
    </script>
@endsection

@section('script')
@endsection
