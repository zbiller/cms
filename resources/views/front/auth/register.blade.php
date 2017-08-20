@extends('layouts::default.default')

@section('content')
    <h1>Register</h1>

    {!! form()->open(['method' => 'POST', 'url' => route('register')]) !!}

    @foreach($errors->all() as $error)
        <span style="color: red;">{{ $error }}</span><br /><br />
    @endforeach

    <label>Username</label><br />
    {!! form()->text('username', old('username') ?: null) !!}<br /><br />
    <label>Password</label><br />
    {!! form()->password('password') !!}<br /><br />
    <label>Confirm Password</label><br />
    {!! form()->password('password_confirmation') !!}<br /><br />
    <label>First Name</label><br />
    {!! form()->text('person[first_name]', old('person.first_name') ?: null) !!}<br /><br />
    <label>Last Name</label><br />
    {!! form()->text('person[last_name]', old('person.last_name') ?: null) !!}<br /><br />
    <label>Email</label><br />
    {!! form()->text('person[email]', old('person.email') ?: null) !!}<br /><br />
    {!! form()->submit('Reset Password') !!}
    {!! form()->close() !!}
@endsection