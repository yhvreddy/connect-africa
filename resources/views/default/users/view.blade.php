@extends('layouts.simple.master')

@section('title', 'User Details')

@section('css')

@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <h3>User Details</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item"><a href="{{url()->previous()}}">Users</a></li>
    <li class="breadcrumb-item active">User Details</li>
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
                            <h5>{{$user->name}}</h5>
                            <span>Tranche {{count($user->affiliates) === 0?1:count($user->affiliates)}} User In {{$user->country->name ?? null}}</span>
                       </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <h5>User trackline</h5>
                        @if(count($userParentReferrals))
                            @foreach($userParentReferrals as $key => $userParentReferralUser)
                                @if($key <= 3)
                                    <div class="col-12 mt-2 mb-2">
                                        <div class="row">
                                            <div class="col-1 text-center">
                                                <span style="padding: 8px 15px;font-size: 24px;font-weight: bold;background: black;color: white;border-radius: 40%;position: relative;top: 15px;">{{$key+1}}</span>
                                            </div>
                                            <div class="col-11">
                                                <div class="row">
                                                    <div>{{date('M d, Y', strtotime($userParentReferralUser->created_at))}}</div>
                                                    <div>{{$userParentReferralUser->name}}</div>
                                                    <div>{{$userParentReferralUser->role->title}} Code {{strtoupper($userParentReferralUser->access_code)}} {{$userParentReferralUser->role->title}} Name: ({{$userParentReferralUser->name}})</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>



                    <div class="row mt-5">
                        <h5>Referred Users (Code {{$user->access_code}})</h5>
                        @if(count($referredUsers))
                            @foreach($referredUsers as $key => $referredUser)
                                @if($key <= 3)
                                    <div class="col-12 mt-2 mb-2">
                                        <div class="row">
                                            <div class="col-1 text-center">
                                                <span style="padding: 8px 15px;font-size: 24px;font-weight: bold;background-color:#34FF94;color: #000000;border-radius: 40%;position: relative;top: 15px;">{{$key+1}}</span>
                                            </div>
                                            <div class="col-11">
                                                <div class="row">
                                                    <div>{{date('M d, Y', strtotime($referredUser->created_at))}}</div>
                                                    <div>{{$referredUser->name}}</div>
                                                    <div>{{$referredUser->role->title}} Code {{strtoupper($referredUser->access_code)}}  {{$referredUser->role->title}} Name: ({{$referredUser->name}})</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="col-12 mt-2 mb-2 p-2 text-center">
                                <h6>No referred users found.</h6>
                            </div>
                        @endif
                    </div>


                    @if($currentUser && $currentUser->role_id === 1)
                        {{-- <div class="row mt-5">
                            <h5>Support App Login Details</h5>
                        </div> --}}
                    @endif
                </div>
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
