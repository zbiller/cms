@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Pages</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.pages._filter')
    </section>

    <section id="tree-container" class="list list-small left">
        @include('admin.cms.pages._tree')
    </section>

    <section id="pages-container" class="list list-big right">
        @include('admin.cms.pages._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! button()->action('Deleted Pages', route('admin.pages.deleted'), 'fa-trash', 'red no-margin-left') !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.pages.create') !!}
    </section>
@endsection