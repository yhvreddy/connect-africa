@extends('layouts.simple.master')

@section('title', 'Edit Patner')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Partner</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Partners</li>
    <li class="breadcrumb-item active">Edit Partner</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Partner</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.partners.update', ['partner' => $partner->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="territorName">Select Territory Name</label>
                                <select class="js-example-basic-single col-sm-12" required id="territorName" name="country_id">
                                    <option value="">Select Territory Name</option>
                                    @foreach ($countries as $key => $country)
                                        <option value="{{$country->id}}"  {{$partner->country_id != $country->id?:'selected'}} >{{$country->name}}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('country_id'))
                                    <span class="text-danger">{{ $errors->first('country_id') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminName">Name</label>
                                <input class="form-control" id="adminName" type="text" name="name" value="{{$partner->name}}" required="" />
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminEmail">Email / Username</label>
                                <input class="form-control" id="adminEmail" type="email" name="email" required="" value="{{$partner->email}}" />
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <input type="hidden" value="{{$partner->share_amount??0}}" name="share_amount" />
                            <input type="hidden" value="{{$partner->user_caps??0}}" name="user_caps" />

                            {{-- <div class="col-md-4">
                                <label class="form-label" for="territoryCode">Partner Share (USD)</label>
                                <input class="form-control"  value="{{old('share_amount') ?? $partner->share_amount}}" id="territoryCode" onkeypress="return restrictToFloat(event)" type="text" name="share_amount" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter territory name.</div>
                                @if($errors->has('share_amount'))
                                    <span class="text-danger">{{ $errors->first('share_amount') }}</span>
                                @endif
                            </div> --}}

                            {{-- <div class="col-md-4">
                                <label class="form-label" for="territoryCode">User Cap</label>
                                <input class="form-control" id="territoryCode" value="{{old('country_code') ?? $partner->user_caps}}" onkeypress="return isNumberKey(event)" type="text" name="user_caps" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter user cap.</div>
                                @if($errors->has('user_caps'))
                                    <span class="text-danger">{{ $errors->first('user_caps') }}</span>
                                @endif
                            </div> --}}

                            <div class="col-md-4">
                                <label class="form-label" for="adminPassword">Update Password</label>
                                <a href="javascript:0;" class="float-end show_hide_password show_password">Show Password</a>
                                <input class="form-control" id="adminPassword" type="password" name="password">
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                        </div>


                        <button class="btn btn-primary custom_btn_black" type="submit">Update Partner</button>
                    </form>
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
    <script src="{{ asset('assets/js/form-validation-custom.js') }}"></script>
    <script src="{{asset('assets/src/js/script.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('.show_hide_password').on('click', function(event) {
                event.preventDefault();
                var passwordField = $('#adminPassword');
                var passwordFieldType = passwordField.attr('type');

                if(passwordFieldType == 'password') {
                    passwordField.attr('type', 'text');
                    $(this).text('Hide Password');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).text('Show Password');
                }
            });
        });
    </script>
@endsection
