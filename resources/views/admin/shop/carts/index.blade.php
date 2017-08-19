@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.carts._filter')
    </section>

    <section class="list">
        @include('admin.shop.carts._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if($paginate)
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updateAction() !!}

        {!! form()->open(['url' => route('admin.carts.remind'), 'method' => 'POST', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-bell"></i>&nbsp; Remind users of their cart', ['type' => 'submit', 'class' => 'btn green', 'onclick' => 'return confirm("Are you sure you want to notify by email all of the users that have a pending cart?")']) !!}
        {!! form()->close() !!}

        @if((int)config('shop.cart.delete_records_older_than') > 0)
            {!! form()->open(['url' => route('admin.carts.clean'), 'method' => 'DELETE', 'class' => 'left']) !!}
            {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup carts older than ' . (int)config('shop.cart.delete_records_older_than') . ' days', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure you want to clean the carts?")']) !!}
            {!! form()->close() !!}
        @endif
    </section>
@endsection