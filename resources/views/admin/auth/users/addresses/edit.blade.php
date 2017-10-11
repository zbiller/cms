@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.users.edit', $user) }}" class="real-tab">Back To User</a>
    </section>

    <section class="tabs">
        @include('admin.auth.users.addresses._tabs')
    </section>

    <section class="view">
        @include('admin.auth.users.addresses._form', ['url' => route('admin.addresses.update', ['user' => $user->id, 'id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.auth.users.addresses._buttons')
@endsection

