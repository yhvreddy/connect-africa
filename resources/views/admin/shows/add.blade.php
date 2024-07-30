@extends('layouts.simple.master')
@section('title', 'Add Show')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>Add Show</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Shows</li>
<li class="breadcrumb-item active">Add Show</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					<h5>Add New Show</h5>
				</div>
				<form class="form theme-form needs-validation" method="POST" novalidate="" enctype="multipart/form-data" action="{{route('default.shows.store')}}">
					@csrf
					<div class="card-body">
						<div class="row">

							<div class="col">
								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Poster Image</label>
									<div class="col-sm-9">
										<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="poster_image" required="" />
										@if($errors->has('poster_image'))
											<span class="text-danger">{{ $errors->first('poster_image') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Title</label>
									<div class="col-sm-9">
										<input class="form-control" type="text" required="" name="title" value="{{old('title')}}" />
										@if($errors->has('title'))
											<span class="text-danger">{{ $errors->first('title') }}</span>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-sm-3 col-form-label">Year</label>
									<div class="col-sm-9">
										<select class="form-control js-example-basic-single_" required="" name="year">
											<option value="">Choose Year</option>
											@foreach ($years as $year)
												<option value="{{$year}}">{{$year}}</option>
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
												<option value="{{$rated}}">{{$rated}}</option>
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
										<textarea class="form-control" name="description" rows="5" required=""></textarea>
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
												<option value="{{$rating}}">{{$rating}}</option>
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
												<option value="{{$rating}}">{{$rating}}</option>
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
												<option value="{{$rating}}">{{$rating}}</option>
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
													<input class="form-check-input" name="genres[]" value="{{$genre->id}}" id="inline-{{$genre->id}}" type="checkbox">
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
												<option value="{{$ottPlatform->id}}">{{$ottPlatform->title}}</option>
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
												<option value="{{$ottPlatform->id}}">{{$ottPlatform->title}}</option>
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
												<option value="{{$ottPlatform->id}}">{{$ottPlatform->title}}</option>
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
												<option value="{{$ottPlatform->id}}">{{$ottPlatform->title}}</option>
											@endforeach
										</select>
										@if($errors->has('free_option'))
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
														<input class="form-check-input" name="categorizes[]" value="{{$categorize->id}}" id="categorize_inline-{{$categorize->id}}" type="checkbox">
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
											<div class="row mb-3">
												<div class="col-12">
													<input class="form-control" type="file" accept=".png, .jpg, .jpeg" name="videos[0][image]" />
												</div>
												<div class="col-12 mt-2">
													<input class="form-control" type="url" placeholder="video source link" name="videos[0][link]" />
												</div>
											</div>
										</div>

										<button class="btn btn-success add_button" type="button">Add More</button>
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
	<script>
		$(document).ready(function(){
			var maxField = 5; //Input fields increment limitation
			var addButton = $('.add_button'); //Add button selector
			var wrapper = $('.field_wrapper'); //Input field wrapper
			var x = 1; //Initial field counter is 2

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
