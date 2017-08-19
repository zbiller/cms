@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.blocks._filter')
    </section>

    <section class="list">
        @include('admin.cms.blocks._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->deletedRecords(route('admin.blocks.deleted')) !!}
        {!! button()->draftedRecords(route('admin.blocks.drafts')) !!}
        {!! button()->addRecord(route('admin.blocks.create')) !!}
    </section>
@endsection