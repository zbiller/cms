@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Page</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.pages._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.pages.store', ['parent' => $parent ?: null]), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.pages._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.pages.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection

{!! js()->sluggify('name', 'slug') !!}