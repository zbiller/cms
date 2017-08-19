@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.location.countries._filter')
    </section>

    <section class="list">
        @include('admin.location.countries._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.countries.create')) !!}
    </section>
@endsection