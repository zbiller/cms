@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.emails._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.emails.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
        @include('admin.cms.emails._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.emails.index')) !!}
    </section>
    <section class="actions">
        {!! button()->duplicateRecord(route('admin.emails.duplicate', $item->id)) !!}
        {!! button()->previewRecord(route('admin.emails.preview', $item->id)) !!}
        {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection

