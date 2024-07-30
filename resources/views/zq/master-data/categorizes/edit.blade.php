@extends('layouts.simple.master')

@section('title', 'Edit Categorize')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Categorize</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Master data</li>
    <li class="breadcrumb-item active">Edit Categorize</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid d-flex align-items-center justify-content-center">
        <div class="col-sm-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.categorizes.update', ['categorize' => $categorize->id])}}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">

                            <div class="col-md-12">
                                <label class="form-label" for="type">Type <span>*</span></label>
                                <select class="form-control" required id="type" name="type">
                                    <option value="">Select Type</option>
                                    <option {{$categorize->type != 'movies'?:'selected'}} value="movies">Movies</option>
                                    <option {{$categorize->type != 'shows'?:'selected'}} value="shows">Shows</option>
                                </select>
                                @if($errors->has('type'))
                                    <span class="text-danger">{{ $errors->first('type') }}</span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="title">Title</label>
                                <input class="form-control" id="title" type="text" name="title" required="" value="{{old('title') ?? $categorize->title}}">
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="col-md-12">

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Is On Menu ?</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" id="isOnMenuYes" type="radio" name="is_in_menu" value="1" data-bs-original-title="" {{$categorize->is_in_menu == 1?'checked':''}} title="">
                                            <label class="form-check-label mb-0" for="isOnMenuYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" {{$categorize->is_in_menu == 0?'checked':''}} id="isOnMenuNo" type="radio" name="is_in_menu" value="0" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnMenuNo">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Is Show Frontend ?</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" {{$categorize->is_show_frontend == 1?'checked':''}} id="isOnFrontendYes" type="radio" name="is_show_frontend" value="1" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnFrontendYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" {{$categorize->is_show_frontend == 0?'checked':''}} id="isOnFrontendNo" type="radio" name="is_show_frontend" value="0" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnFrontendNo">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn-primary custom_btn_black" type="submit">Update Categorize</button>
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
