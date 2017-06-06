@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Role</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.roles._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.roles.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.roles._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.roles.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection