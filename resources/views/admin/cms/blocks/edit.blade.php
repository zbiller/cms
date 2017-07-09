@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.blocks._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.blocks.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.blocks._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.blocks.index')) !!}
    </section>
    <section class="actions">
        {!! button()->duplicateRecord(route('admin.blocks.duplicate', $item->id)) !!}
        {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection