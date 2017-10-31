@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.pages._filter')
    </section>

    <section id="tree-container" class="list list-small left">
        @include('admin.cms.pages._tree')
    </section>

    <section id="pages-container" class="list list-big right">
        @include('admin.cms.pages._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->deletedRecords(route('admin.pages.deleted')) !!}
        {!! button()->draftedRecords(route('admin.pages.drafts')) !!}
        {!! button()->addRecord(route('admin.pages.create')) !!}
    </section>
@endsection