@extends('layouts.simple.master')

@section('title', 'Edit Country')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Country</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">Edit Country</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.countries.update', ['country' => $country->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                        
                            <div class="col-md-10">
                                <label class="form-label" for="countryName">Name  <span>*</span></label>
                                <input class="form-control" placeholder="Country Name" id="countryName" value="{{old('name') ?? $country->name}}" type="text" name="name" required="">
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-2 pt-4">
                                <button class="btn btn-primary custom_btn_black" type="submit">Update Country</button>
                            </div>

                        </div>
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
@endsection
