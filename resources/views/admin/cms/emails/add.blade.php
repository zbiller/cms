@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.emails._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.emails.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.emails._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.emails.index')) !!}
    </section>
    <section class="actions">
        {!! button()->previewRecord(route('admin.emails.preview', $item->id)) !!}
        {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection