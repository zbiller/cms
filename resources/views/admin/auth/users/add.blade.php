@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.auth.users._tabs')
    </section>

    <section class="view">
        @include('admin.auth.users._form', ['url' => route('admin.users.store')])
    </section>
@endsection

@section('footer')
    @include('admin.auth.users._buttons')
@endsection