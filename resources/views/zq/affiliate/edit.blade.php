@extends('layouts.simple.master')

@section('title', 'Edit Affiliate')

@section('css')

@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Affiliate</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Affiliate</li>
    <li class="breadcrumb-item active">Edit Affiliate</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Affiliate</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.affiliates.update', ['affiliate' => $affiliate->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="affiliateName">Name</label>
                                <input class="form-control" id="affiliateName" type="text" name="name" value="{{$affiliate->name}}" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter name.</div>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="affiliateEmail">Email</label>
                                <input class="form-control" id="affiliateEmail" type="email" name="email" required="" value="{{$affiliate->email}}" />
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="AffiliateAccessCode">Access Code</label>
                                <input class="form-control" id="AffiliateAccessCode" type="text" name="access_code" required="" value="{{$affiliate->access_code}}"  maxlength="6" />
                                @if($errors->has('access_code'))
                                    <span class="text-danger">{{ $errors->first('access_code') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="territorName">Select Territory Name</label>
                                <select class="js-example-basic-single col-sm-12" required id="territorName" name="country_id">
                                    <option value="">Select Country Name</option>
                                    @foreach ($countries as $country)
                                        <option value="{{$country->id}}" {{$affiliate->country_id != $country->id?:'selected'}} >{{$country->name}}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('country_id'))
                                    <span class="text-danger">{{ $errors->first('country_id') }}</span>
                                @endif
                            </div>

                        </div>


                        <button class="btn btn-primary custom_btn_black" type="submit">Update Affiliate</button>
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
    <script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
@endsection
