@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.localisation.currencies._filter')
    </section>

    <section class="content">
        {!! form()->open(['url' => route('admin.currencies.exchange'), 'method' => 'PUT']) !!}
        {!! form()->button('<i class="fa fa-money"></i>&nbsp; Update exchange rates relative to ' . config('shop.price.default_currency'), ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to update all currency exchange rates relative to the default currency?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.localisation.currencies._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.currencies.create')) !!}
    </section>
@endsection