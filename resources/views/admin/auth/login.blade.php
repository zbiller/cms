@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! Form::open(['url' => route('admin.login')]) !!}
        {!! Form::text('username', null, ['placeholder' => 'Username']) !!}
        {!! Form::password('password', ['placeholder' => 'Password']) !!}
        {!! Form::submit('Sign in') !!}
        {!! Form::close() !!}
        <a href="#">I forgot my password</a>
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