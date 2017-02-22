@extends('layouts::errors.default')

@section('title', 'Error 500')

@section('content')
    <h1>500</h1>
    <h2>Internal Server Error</h2>
    <p>
        <strong>What does this mean?</strong><br />
        Something went wrong on our servers while we were processing your request.
        An error has occurred and this resource cannot be displayed.
        This occurrence has been logged, and a team has been dispatched to deal with your problem.
        We're really sorry about this, and will work hard to get this resolved as soon as possible.
    </p>
    @parent
@endsection