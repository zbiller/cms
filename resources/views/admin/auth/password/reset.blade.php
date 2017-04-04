@extends('layouts::admin.login')

@section('content')
    <div class="header">
        Zbiller
    </div>
    <div class="content">
        {!! validation('admin')->errors() !!}

        {!! form()->open(['url' => route('admin.password.reset')]) !!}
        {!! form()->hidden('token', $token) !!}
        {!! form()->text('username', $username or old('username'), ['placeholder' => 'Username']) !!}
        {!! form()->password('password', ['placeholder' => 'Password']) !!}
        {!! form()->password('password_confirmation', ['placeholder' => 'Confirm password']) !!}
        {!! form()->submit('Reset Password') !!}
        {!! form()->close() !!}

        <a href="{{ route('admin.login') }}">Back to login</a>
    </div>
@endsection

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\ResetPasswordRequest::class) !!}
@append