@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Admin Role</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.admin_roles._tabs')
    </section>

    <section class="view">
        {!! adminform()->open(['url' => route('admin.admin_roles.store'), 'method' => 'POST', 'class' => 'form']) !!}
            @include('admin.acl.admin_roles._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.admin_roles.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection