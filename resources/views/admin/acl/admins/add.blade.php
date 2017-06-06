@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Admin</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.admins._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.admins.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.admins._form')
        {!! form_admin()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.admins.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection