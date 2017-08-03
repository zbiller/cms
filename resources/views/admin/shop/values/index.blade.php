@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.sets.edit', $set) }}" class="real-tab">Back To Set</a>
        <a href="{{ route('admin.attributes.edit', ['set' => $set, 'attribute' => $attribute]) }}" class="real-tab">Back To Attribute</a>
    </section>

    <section class="filters">
        @include('admin.shop.values._filter')
    </section>

    <section class="list">
        @include('admin.shop.values._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if(count(request()->all()))
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->addRecord(route('admin.values.create', ['set' => $set, 'attribute' => $attribute])) !!}
    </section>
@endsection