@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Menu</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.menus._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.menus.store', ['location' => $location, 'parent' => $parent ?: null]), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.menus._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.menus.index', ['location' => $location]) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->save() !!}
    </section>
@endsection