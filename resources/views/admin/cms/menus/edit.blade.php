@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Menu</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.menus._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.menus.update', ['location' => $location, 'id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.menus._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.menus.index', ['location' => $location]) !!}
    </section>
    <section class="actions">
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection