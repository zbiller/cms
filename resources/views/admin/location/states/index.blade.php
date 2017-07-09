@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.location.states._filter')
    </section>

    <section class="list">
        @include('admin.location.states._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->addRecord(route('admin.states.create')) !!}
    </section>
@endsection