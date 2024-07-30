@extends('layouts.simple.master')
@section('title', 'Edit Movie')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>Edit Movie : {{$movie->title}}</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Movies</li>
<li class="breadcrumb-item active">Edit Movie</li>
@endsection

@section('content')

@php
	$savedGenres = $movie->getAdditionalDataByType('movie_genres')->pluck('em_id')->toArray();
	$oneOption = $movie->getAdditionalDataByType('movie_wp_one')->pluck('em_id')->toArray();
	$twoOption = $movie->getAdditionalDataByType('movie_wp_two')->pluck('em_id')->toArray();
	$threeOption = $movie->getAdditionalDataByType('movie_wp_three')->pluck('em_id')->toArray();
	$freeOption = $movie->getAdditionalDataByType('movie_free_option')->pluck('em_id')->toArray();
	$savedVideos = $movie->getAdditionalDataByType('movies_video')->get();
	$categorizeIds = $movie->getAssignedCategorizedList('movies')->pluck('categorize_id')->toArray();
@endphp

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5 class="title">Edit Movie</h5>
				</div>
				<form class="form theme-form needs-validation" action="{{route('default.movies.update', $movie->id)}}" method="POST" novalidate="" enctype="multipart/form-data">
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
											<div class="{{(!empty($movie->poster_image) && Storage::exists($movie->poster_image))?'col-11':'col-12' }}">
												<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="poster_image" {{(!empty($movie->poster_image) && Storage::exists($movie->poster_image))?:'required' }}  />
												<div class="valid-feedback">Looks good!</div>
												<div class="invalid-feedback">Please Upload Currect File format (png, jpg, jpeg).</div>
											</div>
											@if(!empty($movie->poster_image) && Storage::exists($movie->poster_image))
												<div class="col-1 pt-1">
													<a href="{{asset(Storage::url($movie->poster_image))}}" target="_blank">
														<img src="{{asset(Storage::url($movie->poster_image))}}" style="width:38px;" alt="{{$movie->title}}" />
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
										<input class="form-control" type="text" required="" value="{{$movie->title}}" name="title" value="{{old('title')}}" />
										@if($errors->has('title'))
											<span class="text-danger">{{ $errors->first('title') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Year</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="year">
											<option>Choose Year</option>
											@foreach ($years as $year)
												<option {{$movie->year != $year?'':'selected'}} value="{{$year}}">{{$year}}</option>
											@endforeach
										</select>
										@if($errors->has('year'))
											<span class="text-danger">{{ $errors->first('year') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Rating</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="rating">
											<option value="">Choose Rated</option>
											@foreach ($ratedList as $rated)
												<option value="{{$rated}}" {{$movie->rating != $rated?:'selected'}}>{{$rated}}</option>
											@endforeach
										</select>
										@if($errors->has('rating'))
											<span class="text-danger">{{ $errors->first('rating') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Description</label>
									<div class="col-sm-9">
										<textarea class="form-control" name="description" rows="5" required="">{{$movie->description}}</textarea>
										@if($errors->has('description'))
											<span class="text-danger">{{ $errors->first('description') }}</span>
										@endif
									</div>
								</div>

								{{-- <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">IMDB Score</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="imdb_score">
											<option value="">Choose IMDB Score</option>
											@foreach ($ratings as $rating)
												<option value="{{$rating}}" {{$movie->imdb_score != $rating?:'selected'}}>{{$rating}}</option>
											@endforeach
										</select>
										@if($errors->has('imdb_score'))
											<span class="text-danger">{{ $errors->first('imdb_score') }}</span>
										@endif
									</div>
								</div> --}}

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Truflix Score</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="truflix_score">
											<option value="">Truflix Score</option>
											@foreach ($ratings as $rating)
												<option value="{{$rating}}" {{$movie->truflix_score != $rating?:'selected'}}>{{$rating}}</option>
											@endforeach
										</select>
										@if($errors->has('truflix_score'))
											<span class="text-danger">{{ $errors->first('truflix_score') }}</span>
										@endif
									</div>
								</div>

								{{-- <div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Google Score</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="google_score">
											<option value="">Choose Google Score</option>
											@foreach ($ratings as $rating)
												<option value="{{$rating}}" {{$movie->google_score != $rating?:'selected'}}>{{$rating}}</option>
											@endforeach
										</select>
										@if($errors->has('google_score'))
											<span class="text-danger">{{ $errors->first('google_score') }}</span>
										@endif
									</div>
								</div> --}}

								<div class="mb-3 row">
								<label class="col-sm-3 col-form-label">Genres</label>
								<div class="col-sm-9">
									<div class="row">
										<div class="m-t-15 m-checkbox-inline">
											@foreach ($genres as $genre)
												<div class="form-check form-check-inline checkbox checkbox-dark mb-0">
													<input class="form-check-input" {{!in_array($genre->id, $savedGenres)?:'checked'}} name="genres[]" value="{{$genre->id}}" id="inline-{{$genre->id}}" type="checkbox">
													<label class="form-check-label" for="inline-{{$genre->id}}">{{$genre->title}}</label>
												</div>
											@endforeach
										</div>
									</div>
									@if($errors->has('genres'))
										<span class="text-danger">{{ $errors->first('genres') }}</span>
									@endif
								</div>
							</div>


								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 1</label>
									<div class="col-sm-9">
                                        <select class="form-control js-example-basic-single_" required="" name="wp_one">
											<option value="">Choose Watch Option 1</option>
											@foreach ($ottPlatforms as $ottPlatform)
												<option value="{{$ottPlatform->id}}" {{!in_array($ottPlatform->id, $oneOption)?:'selected'}} >{{$ottPlatform->title}}</option>
											@endforeach
										</select>
										@if($errors->has('wp_one'))
											<span class="text-danger">{{ $errors->first('wp_one') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 2</label>
									<div class="col-sm-9">
                                        <select class="form-control js-example-basic-single_"  name="wp_two">
											<option value="">Choose Watch Option 2</option>
											@foreach ($ottPlatforms as $ottPlatform)
												<option value="{{$ottPlatform->id}}" {{!in_array($ottPlatform->id, $twoOption)?:'selected'}} >{{$ottPlatform->title}}</option>
											@endforeach
										</select>
										@if($errors->has('wp_two'))
											<span class="text-danger">{{ $errors->first('wp_two') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 3</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" name="wp_three">
											<option value="">Choose Watch Option 3</option>
											@foreach ($ottPlatforms as $ottPlatform)
												<option value="{{$ottPlatform->id}}" {{!in_array($ottPlatform->id, $threeOption)?:'selected'}} >{{$ottPlatform->title}}</option>
											@endforeach
										</select>
										@if($errors->has('wp_three'))
											<span class="text-danger">{{ $errors->first('wp_three') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Watch Option 4</label>
									<div class="col-sm-9">
                                        <select class="form-control js-example-basic-single_" name="free_option">
											<option value="">Choose Watch Option 4</option>
											@foreach ($ottPlatforms as $ottPlatform)
												<option value="{{$ottPlatform->id}}" {{!in_array($ottPlatform->id, $freeOption)?:'selected'}} >{{$ottPlatform->title}}</option>
											@endforeach
										</select>
										@if($errors->has(''))
											<span class="text-danger">{{ $errors->first('free_option') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Categorize</label>
									<div class="col-sm-9">
										<div class="row">
											<div class="m-t-15 m-checkbox-inline">
												@foreach ($categorizes as $categorize)
													<div class="form-check form-check-inline checkbox checkbox-dark mb-0">
														<input class="form-check-input" name="categorizes[]" value="{{$categorize->id}}" id="categorize_inline-{{$categorize->id}}" {{!in_array($categorize->id, $categorizeIds)?:'checked'}}  type="checkbox">
														<label class="form-check-label" for="categorize_inline-{{$categorize->id}}">{{$categorize->title}}</label>
													</div>
												@endforeach
											</div>
										</div>
										@if($errors->has('categorizes'))
											<span class="text-danger">{{ $errors->first('categorizes') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Videos</label>
									<div class="col-sm-9">
										<div class="field_wrapper">
											@if(count($savedVideos))
												@foreach ($savedVideos as $key => $savedVideo)
													<div class="row mb-3" id="videoDivId_{{$savedVideo->id}}">
														<div class="col-10">
															<div class="row">
																<div class="col-12">
																	<div class="row">
																		<input type="hidden" value="{{$savedVideo->id}}" name="update_videos[{{$key}}][id]" />
																		<div class="{{(!empty($savedVideo->image) && Storage::exists($savedVideo->image))?'col-11':'col-12' }}">
																			<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="update_videos[{{$key}}][image]" {{(!empty($savedVideo->image) && Storage::exists($savedVideo->image))?:'required' }}  />
																		</div>
																		@if(!empty($savedVideo->attachment) && Storage::exists($savedVideo->attachment))
																			<div class="col-1 pt-1">
																				<a href="{{asset(Storage::url($savedVideo->image))}}" target="_blank">
																					<img src="{{asset(Storage::url($savedVideo->image))}}" style="width:38px;height: 40px;" alt="{{$movie->title}}" />
																				</a>
																			</div>
																		@endif
																	</div>
																</div>
																<div class="col-12 mt-2">
																	<input class="form-control" type="url" placeholder="video source link" name="update_videos[{{$key}}][link]" value="{{$savedVideo->url}}" required="">
																</div>
															</div>
														</div>
														<div class="col-2">
															<a href="javascript:void(0);" data-delete_link="{{route('default.movies.video.delete', ['video'=>$savedVideo->id])}}" data-id="{{$savedVideo->id}}" class="delete_uploaded_video btn btn-danger float-start"><i class="fa fa-trash-o"></i></a>
														</div>
													</div>
												@endforeach
											@else
												<div class="row mb-3">
													<div class="col-12">
														<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="videos[0][image]" />
													</div>
													<div class="col-12 mt-2">
														<input class="form-control" type="url" placeholder="video source link" name="videos[0][link]" />
													</div>
												</div>
											@endif
										</div>

										<button class="btn btn-success add_button" type="button">Add More</button>
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

	<input type="hidden" value="{{count($savedVideos)?? 1}}" id="savedVideosCount" />
@endsection

@section('script')
	<script src="{{asset('assets/js/form-validation-custom.js')}}"></script>
  	<script src="{{asset('assets/src/js/script.js')}}"></script>
  	<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
	<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
	<script>
		$(document).ready(function(){
			var maxField = 5; //Input fields increment limitation
			var addButton = $('.add_button'); //Add button selector
			var wrapper = $('.field_wrapper'); //Input field wrapper
			var x = $("#savedVideosCount").val(); //Initial field counter is 2

			// Once add button is clicked
			$(addButton).click(function(){
				//Check maximum number of input fields
				if(x < maxField){
					$(wrapper).append(addMoreVideoFields(x)); //Add field html
					x++; //Increase field counter
				}else{
					alert('A maximum of '+maxField+' fields are allowed to be added. ');
				}
			});

			// Once remove button is clicked
			$(wrapper).on('click', '.remove_button', function(e){
				e.preventDefault();
				$(this).parent().parent().remove(); //Remove field html
				x--; //Decrease field counter
			});

			$(wrapper).on("click", ".delete_uploaded_video", function(e){
				e.preventDefault();
				var videoId = $(this).data('id');
				var deleteLink = $(this).data('delete_link');
				if(confirm('Do you want to delete?')){
					$.ajax({
						url: deleteLink,
						method: 'GET',
						dataType: 'JSON',
						success:function(response){
							console.log(response.status);
							if(response.status == true){
								$("#videoDivId_"+videoId).remove(); //Remove field html
								x--; //Decrease field counter
								return true;
							}

							console.log(response);
						},
						error:function(error){
							console.log(error);
						}
					});
				}
				return false;
			});
		});

		function addMoreVideoFields(i){
			return '<div class="row mb-3">\
						<div class="col-10">\
							<div class="row">\
								<div class="col-12">\
									<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="videos['+i+'][image]" required="">\
								</div>\
								<div class="col-12 mt-2">\
									<input class="form-control" type="url" placeholder="video source link" name="videos['+i+'][link]" required="">\
								</div>\
							</div>\
						</div>\
						<div class="col-2">\
							<a href="javascript:void(0);" class="remove_button btn btn-danger">Remove</a>\
						</div>\
					</div>';
		}

	</script>

@endsection
