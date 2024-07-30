@extends('layouts.simple.master')
@section('title', 'Add Event')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>Add Event</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Events</li>
<li class="breadcrumb-item active">Add Event</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5>Add New Event</h5>
				</div>
				<form class="form theme-form needs-validation" method="POST" novalidate="" enctype="multipart/form-data" action="{{route('default.events.store')}}">
					@csrf
					<div class="card-body">
						<div class="row">

							<div class="col">
							    <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Poster Image</label>
									<div class="col-sm-9">
										<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="poster_image" required="">
										<div class="valid-feedback">Looks good!</div>
										<div class="invalid-feedback">Please Upload Currect File format (png, jpg, jpeg).</div>
										@if($errors->has('poster_image'))
											<span class="text-danger">{{ $errors->first('poster_image') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Title</label>
									<div class="col-sm-9">
										<input class="form-control" type="text" required="" name="title" value="{{old('title')}}" />
										<div class="valid-feedback">Looks good!</div>
										<div class="invalid-feedback">Please enter proper title name.</div>
										@if($errors->has('title'))
											<span class="text-danger">{{ $errors->first('title') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Type</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="event_type_id">
											<option value="">Choose Type</option>
											@foreach ($eventTypes as $eventType)
												<option value="{{$eventType->id}}">{{$eventType->title}}</option>
											@endforeach
										</select>
										@if($errors->has('event_type_id'))
											<span class="text-danger">{{ $errors->first('event_type_id') }}</span>
										@endif
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Date</label>
									<div class="col-sm-9">
										<input class="form-control" type="date" required="" name="date" value="{{old('date')}}" />
										@if($errors->has('date'))
											<span class="text-danger">{{ $errors->first('date') }}</span>
										@endif
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Time (GMT)</label>
									<div class="col-sm-9">
										<input class="form-control" type="time" required="" name="time" value="{{old('time')}}" />
										@if($errors->has('time'))
											<span class="text-danger">{{ $errors->first('time') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Description</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="description" rows="5" required=""></textarea>
										@if($errors->has('description'))
											<span class="text-danger">{{ $errors->first('description') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 1</label>
									<div class="col-sm-9">
                                        <div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="text" required="" name="watch_one_title" placeholder="Title" value="{{old('watch_one_title')}}" />
                                                @if($errors->has('watch_one_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_one_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_one_image" required="">
                                                @if($errors->has('watch_one_image'))
                                                    <span class="text-danger">{{ $errors->first('watch_one_image') }}</span>
                                                @endif
                                            </div>
                                        </div>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 2</label>
									<div class="col-sm-9">
										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="text" required="" name="watch_two_title" placeholder="Title" value="{{old('watch_two_title')}}" />
                                                @if($errors->has('watch_two_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_two_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_two_image" required="">
                                                @if($errors->has('watch_two_image'))
                                                    <span class="text-danger">{{ $errors->first('watch_two_image') }}</span>
                                                @endif
                                            </div>
                                        </div>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 3</label>
									<div class="col-sm-9">
										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="text" required="" name="watch_three_title" placeholder="Title" value="{{old('watch_three_title')}}" />
                                                @if($errors->has('watch_three_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_three_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_three_image" required="">
                                                @if($errors->has('watch_three_image'))
                                                    <span class="text-danger">{{ $errors->first('watch_three_image') }}</span>
                                                @endif
                                            </div>
                                        </div>
									</div>
								</div>
							</div>

							<div class="col-sm-12 text-center mb-5 mt-5">
								<button class="btn btn-primary custom_btn_black" type="submit">Save</button>
								{{-- <input class="btn btn-light" type="reset" value="Cancel"> --}}
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
	<script src="{{asset('assets/js/form-validation-custom.js')}}"></script>
  	<script src="{{asset('assets/src/js/script.js')}}"></script>
  	<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
	<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
@endsection
