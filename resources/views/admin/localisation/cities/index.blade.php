@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.localisation.cities._filter')
    </section>

    <section class="list">
        @include('admin.localisation.cities._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.cities.create')) !!}
    </section>
@endsection