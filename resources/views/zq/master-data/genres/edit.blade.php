@extends('layouts.simple.master')

@section('title', 'New Genre')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>New Genre</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Genre</li>
    <li class="breadcrumb-item active">New Genre</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit New Genre</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.genres.update', ['genre' => $genre->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="genreName">Title</label>
                                <input class="form-control" id="genreName" type="text" name="title" required="" value="{{old('title')??$genre->title}}">
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="genreSlug">Slug</label>
                                <input class="form-control" id="genreSlug" type="text" name="slug" value="{{old('slug')??$genre->slug}}" required>
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                        </div>
                        <button class="btn btn-primary custom_btn_black" type="submit">Update Genres</button>
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
