@extends('layouts.simple.master')

@section('title', 'Edit Admin')

@section('css')

@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Edit User</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')

	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit User</h5>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('admin.users.update', ['user' => $user->id])}}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="userName">Name</label>
                                <input class="form-control" id="userName" type="text" name="name" value="{{$user->name}}" required="" />
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="userEmail">Email</label>
                                <input class="form-control" id="userEmail" type="email" name="email" required="" value="{{$user->email}}" />
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="mobile">Mobile</label>
                                <input class="form-control" id="mobile" type="text" name="mobile" required="" value="{{old('mobile') ?? $user->mobile}}" />
                                @if($errors->has('mobile'))
                                    <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                @endif
                            </div>

                            <input type="hidden" name="username" value="{{$user->email}}" />

                            {{-- <div class="col-md-4">
                                <label class="form-label" for="userUsername">Login Username</label>
                                <input class="form-control" id="userUsername" readonly type="text" name="username" required="" value="{{$user->username}}" >
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please login Username.</div>
                                @if($errors->has('username'))
                                    <span class="text-danger">{{ $errors->first('username') }}</span>
                                @endif
                            </div> --}}

                            <div class="col-md-4">
                                <label class="form-label" for="userPassword">Update Password</label>
                                <a href="javascript:0;" class="float-end show_hide_password show_password">Show Password</a>
                                <input class="form-control" id="userPassword" type="password" name="password" />
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                        </div>


                        <button class="btn btn-primary custom_btn_black" type="submit">Update User</button>
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
@endsection
