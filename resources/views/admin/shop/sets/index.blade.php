@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.sets._filter')
    </section>

    <section class="list">
        @include('admin.shop.sets._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if(count(request()->all()))
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->addRecord(route('admin.sets.create')) !!}
    </section>
@endsection