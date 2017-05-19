@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Admin Group</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.admin_users._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.admin_users.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.admin_users._form')
        {!! form_admin()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.admin_users.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection