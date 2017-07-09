@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.pages._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.pages.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.pages._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.pages.index')) !!}
    </section>
    <section class="actions">
        {!! button()->duplicateRecord(route('admin.pages.duplicate', $item->id)) !!}
        {!! button()->previewRecord(route('admin.pages.preview', $item->id)) !!}
        {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection

