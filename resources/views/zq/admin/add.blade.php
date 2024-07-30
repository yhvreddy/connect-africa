@extends('layouts.simple.master')

@section('title', 'New Admin')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>New Admin</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Admin</li>
    <li class="breadcrumb-item active">New Admin</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add New Admin</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.admins.store')}}">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="adminName">Name</label>
                                <input class="form-control" id="adminName" type="text" name="name" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter name.</div>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminEmail">Email / Username</label>
                                <input class="form-control" id="adminEmail" type="email" name="email" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter valid email address.</div>
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminPassword">Login Password</label>
                                <a href="javascript:0;" class="float-end show_hide_password show_password">Show Password</a>
                                <input class="form-control" id="adminPassword" type="password" name="password" required="" />
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                        </div>


                        <button class="btn btn-primary custom_btn_black" type="submit">Add Admin</button>
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
