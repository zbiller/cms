@extends('layouts::admin.login')

@section('content')
    <div class="header">
        {{ setting()->value('company-name') }}
    </div>
    <div class="content">
        {!! validation('admin')->errors() !!}

        {!! form()->open(['url' => request()->url()]) !!}
        {!! form()->text('email', null, ['placeholder' => 'Email']) !!}
        {!! form()->submit('Recover Password') !!}
        {!! form()->close() !!}
        <a href="{{ route('admin.login') }}">Back to login</a>
    </div>
@endsection

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Auth\PasswordForgotRequest::class) !!}
@append