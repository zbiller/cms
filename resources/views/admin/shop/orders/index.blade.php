@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.orders._filter')
    </section>

    <section class="list">
        @include('admin.shop.orders._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->deletedRecords(route('admin.orders.deleted')) !!}
        {!! button()->addRecord(route('admin.orders.create')) !!}
    </section>
@endsection