@extends('layouts.simple.master')

@section('title', 'Affiliate Details')

@section('css')

@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <h3>{{$affiliate->name}}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item"><a href="{{route('zq.affiliates.index')}}">Affiliates</a></li>
    <li class="breadcrumb-item active">Affiliate Details</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">

            <div class="card" style="background-color:#FF3A5F;color: #000000">
                <div class="card-body">
                    <div class="row">
                       <div class="col-md-12">
                            <h5>{{$affiliate->name}}</h5>
                            <span>Affiliate In {{$affiliate->country->name ?? null}}</span>
                       </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="javascript:0;">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{date('M d, Y', strtotime($affiliate->created_at))}}</h4><span class="f-light">Date Added</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="{{route('zq.affiliates.show.users', ['type'=>'all-users', 'affiliate' => $affiliate->id])}}">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{$users->count()}}</h4><span class="f-light">Users</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="{{route('zq.affiliates.show.users', ['type'=>'current-month', 'affiliate' => $affiliate->id])}}">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{$currentMonthRecords->count()}}</h4><span class="f-light">New This Month</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="{{route('zq.affiliates.show.users', ['type'=>'deactivated', 'affiliate' => $affiliate->id])}}">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div>
                                        <h4>{{$deactivatedUsers->count()}}</h4><span class="f-light">Deactivated</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>{{strtoupper($affiliate->access_code)}}</h4><span class="f-light">Current Invite Code</span>
                                </div>
                            </div>
                            {{-- <div class="font-warning f-w-500"><i class="icon-arrow-down icon-rotate me-1"></i><span>-20%</span></div> --}}
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div>
                                    <h4>0</h4><span class="f-light">Code</span>
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
