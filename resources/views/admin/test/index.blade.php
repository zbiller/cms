@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Test</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.test._filter')
    </section>

    <section class="list">
        @include('admin.test._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.test.create') !!}
    </section>
@endsection