@extends('layouts.simple.master')

@section('title', 'Edit OTT')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit OTT</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">OTT</li>
    <li class="breadcrumb-item active">Edit OTT</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.ott.update', ['ott' => $ott->id])}}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="genreName">Title</label>
                                <input class="form-control" id="genreName" type="text" name="title" required="" value="{{old('title') ?? $ott->title}}">
                                @if($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="ottSlug">Slug</label>
                                <input class="form-control" id="ottSlug" type="text" name="slug" required="" value="{{old('slug') ?? $ott->slug}}">
                                @if($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <div class="row">								
                                    <div class="{{(!empty($ott->image) && Storage::exists($ott->image))?'col-10':'col-12' }}">
                                        <input class="form-control" type="file" accept=".jpg, .jpeg, .png, .svg" name="image" {{(!empty($ott->image) && Storage::exists($ott->image))?:'required' }}  />
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please Upload Currect File format (png, jpg, jpeg).</div>
                                    </div>
                                    @if(!empty($ott->image) && Storage::exists($ott->image))
                                        <div class="col-2">
                                            <a href="{{asset(Storage::url($ott->image))}}" target="_blank">
                                                <img src="{{asset(Storage::url($ott->image))}}" style="width:38px;" alt="{{$ott->title}}" />
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                @if($errors->has('image'))
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                @endif
                                
                            </div>

                        </div>
                        <button class="btn btn-primary custom_btn_black" type="submit">Update OTT</button>
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
