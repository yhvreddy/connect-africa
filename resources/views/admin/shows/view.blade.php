@extends('layouts.simple.master')
@section('title', 'View Show')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>View Show</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Shows</li>
<li class="breadcrumb-item active">View Show</li>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $show->title }}</div>

                    <div class="card-body">
                        <p>Poster Image:<img src="{{ Storage::url($show->poster_image) }}" height="200px" alt="..."></p>
                        <p>Description: {{ $show->description }}</p>
                        <p>Year: {{ $show->year }}</p>
                        <p>Rating: {{ $show->rating }}</p>
                        <p>Imdb Score: {{ $show->imdb_score }}</p>
                        <p>Google Score: {{ $show->google_score }}</p>
                        <p>Rotten Tomatoes Score: {{ $show->rt_score }}</p>
                        <p>Genres:
                            @foreach ($availableGenres as $genreId)
                                @php
                                    $genre = App\Models\EntertainmentMasterData::find($genreId);
                                @endphp
                                @if ($genre)
                                    <div class="form-check form-check-inline checkbox checkbox-dark mb-0">
                                        <input class="form-check-input" name="genres[]" value="{{$genre->id}}" id="inline-{{$genre->id}}" type="checkbox" {{ in_array($genre->id, $genres) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="inline-{{$genre->id}}">{{$genre->title}}</label>
                                    </div>
                                @endif
                            @endforeach
                        </p>
                        <p>Watch Option 1: {{ $watchopt1 }}</p>
                        <p>Watch Option 2: {{ $watchopt2 }}</p>
                        <p>Watch Option 3: {{ $watchopt3 }}</p>
                        <p>Free Option: {{ $watchoptfree }}</p>

                            <!-- <label class="col-sm-3 col-form-label">Videos</label> -->
                            <div class="col-sm-9">
                                <div class="field_wrapper">
                                    @foreach ($videos as $key => $video)
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <p>Video Image:<img src="{{ Storage::url($video->image) }}" height="200px" alt="..."></p>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <p >Video Source Link: {{ $video->url }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection