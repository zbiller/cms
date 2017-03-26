@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Library Files</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.library._filter')
    </section>

    <section class="upload">
        @include('admin.cms.library._upload')
    </section>

    <section class="list">
        @include('admin.cms.library._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination()->render($items, 'admin') !!}

    <section class="actions">
        {!! button()->update() !!}
    </section>
@endsection