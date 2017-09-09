@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.localisation.states._filter')
    </section>

    <section class="list">
        @include('admin.localisation.states._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.states.create')) !!}
    </section>
@endsection