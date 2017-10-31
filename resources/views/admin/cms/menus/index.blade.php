@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.menus._filter')
    </section>

    <section id="tree-container" class="list list-small left">
        @include('admin.cms.menus._tree')
    </section>

    <section id="menus-container" class="list list-big right">
        @include('admin.cms.menus._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.menus.create', $location)) !!}
    </section>
@endsection