@extends('layouts.simple.master')

@section('title', 'New OTT')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>New OTT</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">OTT</li>
    <li class="breadcrumb-item active">New OTT</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add New OTT</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.ott.store')}}" enctype="multipart/form-data">
                        @csrf
                        <input id="type" type="hidden" name="type" value="ott_platforms">

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="genreName">Name</label>
                                <input class="form-control" id="genreName" type="text" name="title" required="">
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="genreSlug">Slug</label>
                                <input class="form-control" id="genreSlug" type="text" name="slug" required="">

                                @if($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="ottImage">Image</label>
                                <input class="form-control" id="ottImage" type="file" name="image" accept=".jpg, .jpeg, .png, .svg" required="">
                                @if($errors->has('image'))
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <button class="btn btn-primary custom_btn_black" type="submit">Add OTT</button>
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
