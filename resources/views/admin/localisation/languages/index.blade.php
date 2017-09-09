@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.localisation.languages._filter')
    </section>

    <section class="list">
        @include('admin.localisation.languages._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.languages.create')) !!}
    </section>
@endsection