@extends('layouts::default.default')

@section('content')
    <h1>Reset Password</h1>

    {!! form()->open(['method' => 'POST', 'url' => route('password.reset')]) !!}
    {!! form()->hidden('token', $token) !!}

    @foreach($errors->all() as $error)
        <span style="color: red;">{{ $error }}</span><br /><br />
    @endforeach

    <label>Username</label><br />
    {!! form()->text('username', old('username') ?: null) !!}<br /><br />
    <label>Password</label><br />
    {!! form()->password('password') !!}<br /><br />
    <label>Confirm Password</label><br />
    {!! form()->password('password_confirmation') !!}<br /><br />
    {!! form()->submit('Reset Password') !!}
    {!! form()->close() !!}
@endsection