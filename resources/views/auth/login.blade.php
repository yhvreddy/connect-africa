@extends('layouts.authentication.master')
@section('title', 'Login')

@section('css')
@endsection

@section('style')
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card">
                    <div>
                        <div><a class="logo" href="{{ route('/') }}"><img class="img-fluid for-light"
                                    src="{{ asset('assets/src/images/log.png') }}" alt="looginpage"><img
                                    class="img-fluid for-dark" src="{{ asset('assets/src/images/log.png') }}"
                                    alt="looginpage"></a></div>
                        <div class="login-main">
                            @include('layouts.alerts')
                            <form class="theme-form" method="POST" action="{{ route('login.access') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ request()->role ?? 'admin' }}" />
                                <h4>Sign in to account</h4>
                                <p>Enter your email & password to login</p>
                                <div class="form-group">
                                    <label class="col-form-label">Email Address</label>
                                    <input class="form-control" name="username" type="text" required=""
                                        placeholder="Enter username or email.">
                                    @if ($errors->has('username'))
                                        <span class="text-danger">{{ $errors->first('username') }}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Password</label>
                                    <input class="form-control" name="password" type="password" required=""
                                        placeholder="Enter you login password.">
                                    <div class="show-hide"><span class="show"> </span></div>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-0">
                                    {{-- <div class="checkbox p-0">
                           <input id="checkbox1" type="checkbox">
                           <label class="text-muted" for="checkbox1">Remember password</label>
                        </div> --}}
                                    {{-- <a class="link" href="{{route('forget-password')}}">Forgot password?</a> --}}
                                    <div class="mt-4 text-center">
                                        <button class="btn btn-primary btn-block custom_btn_black" type="submit">Sign
                                            in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
