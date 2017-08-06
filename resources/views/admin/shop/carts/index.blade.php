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
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}

        {{--@if((int)config('activity.delete_records_older_than') > 0)--}}
            {!! form()->open(['url' => route('admin.carts.clean'), 'method' => 'DELETE', 'class' => 'left']) !!}
            {{--{!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup Carts Older Than ' . (int)config('activity.delete_records_older_than') . ' Days', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure you want to clean the activity log?")']) !!}--}}
            {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup carts older than 30 days', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure you want to clean the carts?")']) !!}
            {!! form()->close() !!}
        {{--@endif--}}
    </section>
@endsection