@extends('layouts.simple.master')
@section('title', 'View Event')

@section('css')
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>View Event</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{app('truFlix')->getSessionUser()->role->title}}</li>
<li class="breadcrumb-item">Events</li>
<li class="breadcrumb-item active">View Event</li>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                    <div class="card">
                           <div class="card-header">{{ $event->title }}</div>

                            <div class="card-body">
                                <p>Poster Image:<img src="{{ Storage::url($event->poster_image) }}" height="200px" alt="..."></p>
                                <p>Type: {{$event->eventType->title ?? ''}}</p>
                                <p>Date: {{ $event->date }}</p>
                                <p>Time: {{ $event->time }}</p>
                                <p>Description: {{ $event->description }}</p>
                                <p>Watch Option 1: {{ $watchopt1 }}</p>
                                <p>Watch Option 2: {{ $watchopt2 }}</p>
                                <p>Watch Option 3: {{ $watchopt3 }}</p>
                            </div>
                     </div>
             </div>      
        </div>
    </div>
@endsection