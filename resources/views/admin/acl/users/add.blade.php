@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.acl.users._tabs')
    </section>

    <section class="view">
        @include('admin.acl.users._form', ['url' => route('admin.users.store')])
    </section>
@endsection

@section('footer')
    @include('admin.acl.users._buttons')
@endsection