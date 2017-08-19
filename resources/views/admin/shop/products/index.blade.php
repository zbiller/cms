@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.products._filter')
    </section>

    <section class="list">
        @include('admin.shop.products._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    @if(!$orderable)
        {!! pagination('admin')->render($items) !!}
    @endif

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->deletedRecords(route('admin.products.deleted')) !!}
        {!! button()->draftedRecords(route('admin.products.drafts')) !!}
        {!! button()->addRecord(route('admin.products.create')) !!}
    </section>
@endsection