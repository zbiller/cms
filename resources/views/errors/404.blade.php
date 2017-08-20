@extends('layouts::errors.default')

@section('title', 'Error 404')

@section('content')
    <h1>404</h1>
    <h2>Page Not Found!</h2>
    <p>
        <strong>What does this mean?</strong><br />
        You are trying to access a page that doesn't exist, or accessed a broken URL via a third-party system.
    </p>

    @parent
@endsection