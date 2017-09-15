@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.users.edit', $user) }}" class="real-tab">Back To User</a>
    </section>

    <section class="tabs">
        @include('admin.auth.addresses._tabs')
    </section>

    <section class="view">
        @include('admin.auth.addresses._form', ['url' => route('admin.addresses.store', $user->id)])
    </section>
@endsection

@section('footer')
    @include('admin.auth.addresses._buttons')
@endsection