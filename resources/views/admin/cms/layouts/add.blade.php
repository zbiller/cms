@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Layout</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.layouts._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.layouts.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.layouts._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel(route('admin.layouts.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->save() !!}
    </section>
@endsection