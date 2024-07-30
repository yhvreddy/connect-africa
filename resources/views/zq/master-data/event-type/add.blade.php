@extends('layouts.simple.master')

@section('title', 'New Event')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>New Event</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">New Event</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.event-type.store')}}">
                        @csrf
                        <input id="type" type="hidden" name="type" value="event_types">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="genreName">Name <span class="text-red">*</span></label>
                                <input class="form-control" id="genreName" type="text" name="title" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter title.</div>
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="genreSlug">Slug <span class="text-red">*</span></label>
                                <input class="form-control" id="genreSlug" type="text" name="slug" required="">
                                @if($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                            </div>

                        </div>
                    
                        <button class="btn btn-primary custom_btn_black" type="submit">Add Event</button>
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
