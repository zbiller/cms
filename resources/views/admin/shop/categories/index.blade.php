@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.categories._filter')
    </section>

    <section id="tree-container" class="list list-small left">
        @include('admin.shop.categories._tree')
    </section>

    <section id="categories-container" class="list list-big right">
        @include('admin.shop.categories._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->deletedRecords(route('admin.product_categories.deleted')) !!}
        {!! button()->draftedRecords(route('admin.product_categories.drafts')) !!}
        {!! button()->addRecord(route('admin.product_categories.create')) !!}
    </section>
@endsection