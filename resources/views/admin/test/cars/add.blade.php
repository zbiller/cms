@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Admin Role</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.test._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.test.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
        @include('admin.test._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.test.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection