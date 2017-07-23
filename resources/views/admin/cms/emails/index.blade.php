@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.emails._filter')
    </section>

    <section class="list">
        @include('admin.cms.emails._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->deletedRecords(route('admin.emails.deleted')) !!}
        {!! button()->draftedRecords(route('admin.emails.drafts')) !!}
        {!! button()->addRecord(route('admin.emails.create')) !!}
    </section>
@endsection