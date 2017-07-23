@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.acl.admins._tabs')
    </section>

    <section class="view">
        @include('admin.acl.admins._form', ['url' => route('admin.admins.store')])
    </section>
@endsection

@section('footer')
    @include('admin.acl.admins._buttons')
@endsection