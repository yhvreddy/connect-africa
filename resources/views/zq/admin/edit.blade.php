@extends('layouts.simple.master')

@section('title', 'Edit Admin')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit Admin</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item">Admin</li>
    <li class="breadcrumb-item active">Edit Admin</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Admin</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.admins.update', ['admin' => $admin->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="adminName">Name</label>
                                <input class="form-control" id="adminName" type="text" name="name" value="{{$admin->name}}" required="" />
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminEmail">Email / Username</label>
                                <input class="form-control" id="adminEmail" type="email" name="email" required="" value="{{$admin->email}}" />
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>


                            <div class="col-md-4">
                                <label class="form-label" for="adminPassword">Update Login Password</label>
                                <a href="javascript:0;" class="float-end show_hide_password show_password">Show Password</a>
                                <input class="form-control" id="adminPassword" type="password" name="password" />
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                        </div>


                        <button class="btn btn-primary custom_btn_black" type="submit">Update Admin</button>
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
