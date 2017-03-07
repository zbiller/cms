@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! form()->open(['url' => route('admin.password.reset')]) !!}
        {!! form()->hidden('token', $token) !!}
        {!! form()->text('email', $email or old('email'), ['placeholder' => 'Email']) !!}
        {!! form()->password('password', ['placeholder' => 'Password']) !!}
        {!! form()->password('password_confirmation', ['placeholder' => 'Confirm password']) !!}
        {!! form()->submit('Reset Password') !!}
        {!! form()->close() !!}
        <a href="{{ route('admin.login') }}">Back to login</a>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection