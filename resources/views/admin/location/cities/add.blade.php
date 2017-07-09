@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.location.cities._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.cities.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.location.cities._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.cities.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection