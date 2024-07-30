@extends('layouts.simple.master')

@section('title', 'Settings')

@section('css')
    
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('breadcrumb-title')
    <h3>Settings</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.alerts')
    
	<div class="row widget-grid">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" novalidate="" method="POST" autocomplete="false" action="{{route('zq.settings.site.save')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="adminName">Site Name</label>
                                <input class="form-control" id="adminName" type="text" name="name" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter site name.</div>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="supportNumber">Contact Number</label>
                                <input class="form-control" id="supportNumber" type="text" name="mobile" />
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter contact number.</div>
                                @if($errors->has('mobile'))
                                    <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="adminEmail">Contact Email</label>
                                <input class="form-control" id="adminEmail" type="email" name="email" />
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter valid email address.</div>
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label" for="siteLogo">Site Logo</label>
                                <input class="form-control" id="siteLogo" type="file" name="logo" accept=".jpg,.png,.jpeg, .svg" />
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Upload site logo, Its accepts only (jpg, png, jpeg, svg).</div>
                                @if($errors->has('logo'))
                                    <span class="text-danger">{{ $errors->first('logo') }}</span>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label" for="siteFavIcon">Site Fav Icon</label>
                                <input class="form-control" id="siteFavIcon" type="file" name="fav_icon" accept=".jpg,.png,.jpeg,.svg">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Upload site fav icon, Its accepts only (jpg, png, jpeg, svg).</div>
                                @if($errors->has('fav_icon'))
                                    <span class="text-danger">{{ $errors->first('fav_icon') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="copyRightBy">Copyright By</label>
                                <input class="form-control" id="copyRightBy" type="text" name="copy_rights" required="">
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please enter copyrights by.</div>
                                @if($errors->has('copy_rights'))
                                    <span class="text-danger">{{ $errors->first('copy_rights') }}</span>
                                @endif
                            </div>

                            <h5>Social Media</h5>
                            <div class="col-12">
                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Youtube</label>
									<div class="col-sm-9">
										<div class="row">
                                            <div class="col-12 mt-2">
                                                <input class="form-control" type="url" placeholder="Youtube Channel link" name="socialmedia[youtube]" />
                                            </div>
                                        </div>
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Instagram</label>
									<div class="col-sm-9">
										<div class="row">
                                            <div class="col-12 mt-2">
                                                <input class="form-control" type="url" placeholder="Instagram link" name="socialmedia[instagram]" />
                                            </div>
                                        </div>
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Facebook</label>
									<div class="col-sm-9">
										<div class="row">
                                            <div class="col-12 mt-2">
                                                <input class="form-control" type="url" placeholder="Facebook link" name="socialmedia[facebook]" />
                                            </div>
                                        </div>
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Linked In</label>
									<div class="col-sm-9">
										<div class="row">
                                            <div class="col-12 mt-2">
                                                <input class="form-control" type="url" placeholder="LinkedIn link" name="socialmedia[linkedIn]" />
                                            </div>
                                        </div>
									</div>
								</div>
                            </div>
                        </div>
                        
                        
                        <button class="btn btn-primary custom_btn_black" type="submit">Save Settings</button>
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
