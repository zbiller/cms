@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! form()->open(['url' => request()->url()]) !!}
        {!! form()->text('username', null, ['placeholder' => 'Username']) !!}
        {!! form()->password('password', ['placeholder' => 'Password']) !!}
        {!! form()->submit('Sign in') !!}
        {!! form()->close() !!}
        <a href="{{ route('admin.password.forgot') }}">I forgot my password</a>
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