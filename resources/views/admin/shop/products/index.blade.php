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
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->deletedRecords(route('admin.products.deleted')) !!}
        {!! button()->draftedRecords(route('admin.products.drafts')) !!}
        {!! button()->addRecord(route('admin.products.create')) !!}
    </section>
@endsection