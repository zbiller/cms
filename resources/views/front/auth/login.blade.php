@extends('layouts::default.default')

@section('content')
    <h1>Login</h1>

    {!! form()->open(['method' => 'POST', 'url' => route('login')]) !!}

    @foreach($errors->all() as $error)
        <span style="color: red;">{{ $error }}</span><br /><br />
    @endforeach

    <label>Username</label><br />
    {!! form()->text('username', old('username') ?: null) !!}<br /><br />
    <label>Password</label><br />
    {!! form()->password('password') !!}<br /><br />
    {!! form()->submit('Login') !!}
    {!! form()->close() !!}
@endsection