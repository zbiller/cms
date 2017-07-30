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
    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->deletedRecords(route('admin.categories.deleted')) !!}
        {!! button()->draftedRecords(route('admin.categories.drafts')) !!}
        {!! button()->addRecord(route('admin.categories.create')) !!}
    </section>
@endsection