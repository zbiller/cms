@extends('layouts::admin.login')

@section('content')
    <div class="header">
        {{ setting()->value('company-name') }}
    </div>
    <div class="content">
        {!! validation('admin')->errors() !!}
        {!! form()->open(['url' => request()->url()]) !!}
        {!! form()->text('username', null, ['placeholder' => 'Username']) !!}
        {!! form()->password('password', ['placeholder' => 'Password']) !!}
        {!! form()->submit('Sign in') !!}
        {!! form()->close() !!}
        <a href="{{ route('admin.password.forgot') }}">I forgot my password</a>
    </div>
@endsection

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Auth\LoginRequest::class) !!}
@append