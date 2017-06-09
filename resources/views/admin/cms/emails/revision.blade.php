@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>View Page Revision</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Details</a>
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['class' => 'form']) !!}
        <div id="tab-1" class="tab">
            {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
            {!! form_admin()->text('name') !!}
            {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
        </div>
        <div id="tab-2" class="tab">
            @include('admin.cms.emails.types.' . $partial)
        </div>
        {!! form()->close() !!}
    </section>
@endsection

{!! revision()->view($revision, $item) !!}