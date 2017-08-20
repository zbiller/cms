@extends('layouts::default.default')

@section('content')
    <h1>Forgot Password</h1>

    {!! form()->open(['method' => 'POST', 'url' => route('password.forgot')]) !!}

    @foreach($errors->all() as $error)
        <span style="color: red;">{{ $error }}</span><br /><br />
    @endforeach

    {!! form()->text('email', old('email') ?: null) !!}<br /><br />
    {!! form()->submit('Recover Password') !!}
    {!! form()->close() !!}
@endsection