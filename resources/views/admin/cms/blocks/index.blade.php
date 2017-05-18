@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Blocks</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.blocks._filter')
    </section>

    <section class="list">
        @include('admin.cms.blocks._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add(route('admin.blocks.create')) !!}
    </section>
@endsection