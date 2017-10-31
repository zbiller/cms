@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.discounts._filter')
    </section>

    <section class="list">
        @include('admin.shop.discounts._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.discounts.create')) !!}
    </section>
@endsection