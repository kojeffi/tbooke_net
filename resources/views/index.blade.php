<!-- resources/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div>
        <h1>Resources</h1>
        @foreach ($resources as $resource)
            <div>
                <h2>{{ $resource->name }}</h2>
                <p>Institution: {{ $resource->institution }}</p>
                <p>Category: {{ $resource->category }}</p>
                <!-- Add more fields here as needed -->
            </div>
        @endforeach
    </div>
@endsection

