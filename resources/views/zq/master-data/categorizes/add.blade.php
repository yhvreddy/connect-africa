@extends('layouts.simple.master')

@section('title', 'New Categorize')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
    <h3>New Categorize</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">New Categorize</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid d-flex align-items-center justify-content-center">
        <div class="col-sm-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Add New Categorize</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.categorizes.store')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 mb-3">

                            <div class="col-md-12">
                                <label class="form-label" for="type">Type <span>*</span></label>
                                <select class="form-control" required id="type" name="type">
                                    <option value="">Select Type</option>
                                    <option value="movies">Movies</option>
                                    <option value="shows">Shows</option>
                                </select>
                                @if($errors->has('type'))
                                    <span class="text-danger">{{ $errors->first('type') }}</span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="title">Title <span>*</span></label>
                                <input class="form-control" id="title" type="text" name="title" required="">
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
                                            <input class="form-check-input" id="isOnMenuYes" type="radio" name="is_in_menu" value="1" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnMenuYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" checked id="isOnMenuNo" type="radio" name="is_in_menu" value="0" data-bs-original-title="" title="">
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
                                            <input class="form-check-input" id="isOnFrontendYes" type="radio" name="is_show_frontend" value="1" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnFrontendYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline radio radio-primary">
                                            <input class="form-check-input" checked id="isOnFrontendNo" type="radio" name="is_show_frontend" value="0" data-bs-original-title="" title="">
                                            <label class="form-check-label mb-0" for="isOnFrontendNo">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <button class="btn btn-primary custom_btn_black" type="submit">Add Categorize</button>
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
