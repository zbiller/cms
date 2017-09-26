@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.carts._filter')
    </section>

    <section class="content content-half one">
        {!! form()->open(['url' => route('admin.carts.remind'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-bell"></i>&nbsp; Notify users of their pending cart', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to notify by email all of the users that have a pending cart?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-half two">
        {!! form()->open(['url' => route('admin.carts.clean'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove active carts older than ' . (int)config('shop.cart.delete_records_older_than') . ' days', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all user active carts older than the given time limit?")']) !!}
        {!! form()->close() !!}
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
    </section>
@endsection