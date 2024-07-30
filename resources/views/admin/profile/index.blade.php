
@extends('layouts.simple.master')

@section('title', 'Profile')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Profile</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" 
                        action="{{route('admin.profile.update', ['admin' => $user->id])}}" >
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="userName">Name</label>
                                <input class="form-control" id="userName" type="text" name="name" value="{{$user->name}}" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter name.</div>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="userEmail">Email</label>
                                <input class="form-control" id="userEmail" type="email" name="email" required="" value="{{$user->email}}" >
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter valid email address.</div>
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="userPassword">Change Login Password</label>
                                <input class="form-control" id="userPassword" type="password" name="password">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter password.</div>
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                        </div>
                        
                        
                        <button class="btn btn-primary custom_btn_black" type="submit">Update Details</button>
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
