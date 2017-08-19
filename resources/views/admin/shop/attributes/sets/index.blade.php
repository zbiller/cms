@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.attributes.sets._filter')
    </section>

    <section class="list">
        @include('admin.shop.attributes.sets._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if(count(request()->all()))
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.attribute_sets.create')) !!}
    </section>
@endsection