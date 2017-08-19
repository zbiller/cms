@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.currencies._filter')
    </section>

    <section class="list">
        @include('admin.shop.currencies._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}

        {!! form()->open(['url' => route('admin.currencies.exchange'), 'method' => 'PUT', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-money"></i>&nbsp; Update Exchange Rates', ['type' => 'submit', 'class' => 'btn green', 'onclick' => 'return confirm("Are you sure you want to update all currency exchange rates?")']) !!}
        {!! form()->close() !!}

        {!! button()->addRecord(route('admin.currencies.create')) !!}
    </section>
@endsection