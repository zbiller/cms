@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.location.states._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.states.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.location.states._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.states.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection