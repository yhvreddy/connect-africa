
@extends('layouts.simple.master')
@section('title', 'Edit Event')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>Edit Event : {{$event->title}}</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Events</li>
<li class="breadcrumb-item active">Edit Event</li>
@endsection

@section('content')

@php
	$oneOption = $event->getAdditionalDataByType('event_wp_one')->first();
	$twoOption = $event->getAdditionalDataByType('event_wp_two')->first();
	$threeOption = $event->getAdditionalDataByType('event_wp_three')->first();
@endphp

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5 class="title">Edit Event</h5>
				</div>
				<form class="form theme-form needs-validation" action="{{route('default.events.update', $event->id)}}" method="POST" novalidate="" enctype="multipart/form-data">
					@method('PUT')
					@csrf
					<div class="card-body">
						<div class="row">
							@include('layouts.alerts')
							<div class="col">
								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Poster Image</label>
									<div class="col-sm-9">
										<div class="row">
											<div class="{{(!empty($event->poster_image) && Storage::exists($event->poster_image))?'col-11':'col-12' }}">
												<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="poster_image" {{(!empty($event->poster_image) && Storage::exists($event->poster_image))?:'required' }}  />
											</div>
											@if(!empty($event->poster_image) && Storage::exists($event->poster_image))
												<div class="col-1 pt-1">
													<a href="{{asset(Storage::url($event->poster_image))}}" target="_blank">
														<img src="{{asset(Storage::url($event->poster_image))}}" style="width:38px;" alt="{{$event->title}}" />
													</a>
												</div>
											@endif
										</div>
										@if($errors->has('poster_image'))
											<span class="text-danger">{{ $errors->first('poster_image') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Title</label>
									<div class="col-sm-9">
										<input class="form-control" type="text" required="" value="{{$event->title}}" name="title" value="{{old('title')}}" />
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
                                                <option value="{{$eventType->id}}" {{$eventType->id == $event->event_type_id ? 'selected' : ''}}>{{$eventType->title}}</option>
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
										<input class="form-control" type="date" required="" name="date" value="{{$event->date}}" />
										@if($errors->has('date'))
											<span class="text-danger">{{ $errors->first('date') }}</span>
										@endif
									</div>
								</div>

                                <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Time (GMT)</label>
									<div class="col-sm-9">
										<input class="form-control" type="time" required="" name="time" value="{{$event->time}}" />
										@if($errors->has('time'))
											<span class="text-danger">{{ $errors->first('time') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Description</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="description" rows="5" required="">{{$event->description}}</textarea>
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
                                                <input class="form-control" type="text" required="" name="watch_one_title" placeholder="Title" value="{{old('watch_one_title') ?? $oneOption->title}}" />
                                                @if($errors->has('watch_one_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_one_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="{{(!empty($oneOption->image) && Storage::exists($oneOption->image))?'col-11':'col-12' }}">
                                                        <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_one_image" {{(!empty($oneOption->image) && Storage::exists($oneOption->image))?:'required' }}  />
                                                    </div>
                                                    @if(!empty($oneOption->image) && Storage::exists($oneOption->image))
                                                        <div class="col-1 pt-1">
                                                            <a href="{{asset(Storage::url($oneOption->image))}}" target="_blank">
                                                                <img src="{{asset(Storage::url($oneOption->image))}}" style="width:38px;" alt="{{$oneOption->title}}" />
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
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
                                                <input class="form-control" type="text" required="" name="watch_two_title" placeholder="Title" value="{{old('watch_two_title') ?? $twoOption->title}}" />
                                                @if($errors->has('watch_two_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_two_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="{{(!empty($twoOption->image) && Storage::exists($twoOption->image))?'col-11':'col-12' }}">
                                                        <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_two_image" {{(!empty($twoOption->image) && Storage::exists($twoOption->image))?:'required' }}  />
                                                    </div>
                                                    @if(!empty($twoOption->image) && Storage::exists($twoOption->image))
                                                        <div class="col-1 pt-1">
                                                            <a href="{{asset(Storage::url($twoOption->image))}}" target="_blank">
                                                                <img src="{{asset(Storage::url($twoOption->image))}}" style="width:38px;" alt="{{$twoOption->title}}" />
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
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
                                                <input class="form-control" type="text" required="" name="watch_three_title" placeholder="Title" value="{{old('watch_three_title') ?? $threeOption->title}}" />
                                                @if($errors->has('watch_three_title'))
                                                    <span class="text-danger">{{ $errors->first('watch_three_title') }}</span>
                                                @endif
                                            </div>
                                        </div>

										<div class="mb-3 row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="{{(!empty($threeOption->image) && Storage::exists($threeOption->image))?'col-11':'col-12'}}">
                                                        <input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="watch_three_image" {{(!empty($threeOption->image) && Storage::exists($threeOption->image))?:'required' }}  />
                                                    </div>
                                                    @if(!empty($threeOption->image) && Storage::exists($threeOption->image))
                                                        <div class="col-1 pt-1">
                                                            <a href="{{asset(Storage::url($threeOption->image))}}" target="_blank">
                                                                <img src="{{asset(Storage::url($threeOption->image))}}" style="width:38px;" alt="{{$threeOption->title}}" />
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($errors->has('watch_three_image'))
                                                    <span class="text-danger">{{ $errors->first('watch_three_image') }}</span>
                                                @endif
                                            </div>
                                        </div>
									</div>
								</div>
							</div>

							<div class="col-sm-12 text-center mb-5 mt-5">
								<button class="btn btn-primary custom_btn_black" type="submit">Update</button>
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
