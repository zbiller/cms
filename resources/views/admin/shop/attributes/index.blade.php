@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.attribute_sets.edit', $set) }}" class="real-tab">Back To Set</a>
    </section>

    <section class="filters">
        @include('admin.shop.attributes._filter')
    </section>

    <section class="list">
        @include('admin.shop.attributes._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if(count(request()->all()))
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->addRecord(route('admin.attributes.create', $set)) !!}
    </section>
@endsection