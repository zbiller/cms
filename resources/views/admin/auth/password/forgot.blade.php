@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! Form::open(['url' => request()->url()]) !!}
        {!! Form::text('email', null, ['placeholder' => 'Email']) !!}
        {!! Form::submit('Recover Password') !!}
        {!! Form::close() !!}
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