@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Block</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.blocks._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.blocks.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.blocks._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.blocks.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->save() !!}
    </section>
@endsection