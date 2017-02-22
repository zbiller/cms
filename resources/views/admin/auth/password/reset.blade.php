@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! Form::open(['url' => route('admin.password.reset')]) !!}
        {!! Form::hidden('token', $token) !!}
        {!! Form::text('email', $email or old('email'), ['placeholder' => 'Email']) !!}
        {!! Form::password('password', ['placeholder' => 'Password']) !!}
        {!! Form::password('password_confirmation', ['placeholder' => 'Confirm password']) !!}
        {!! Form::submit('Reset Password') !!}
        {!! Form::close() !!}
        {{--<a href="{{ route('admin.login') }}">Back to login</a>--}}
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