@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Layouts</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.layouts._filter')
    </section>

    <section class="list">
        @include('admin.cms.layouts._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.layouts.create') !!}
    </section>
@endsection