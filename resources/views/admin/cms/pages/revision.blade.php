@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>View Page Revision</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Content</a>
        <a href="#tab-3">Meta Tags</a>
        {!! block()->tab($item) !!}
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['class' => 'form']) !!}
            <div id="tab-1" class="tab">
                {!! form_admin()->select('layout_id', 'Layout', $layouts->pluck('name', 'id'), null, ['disabled']) !!}
                {!! form_admin()->select('type', 'Type', $types, null, ['disabled']) !!}
                {!! form_admin()->text('name', 'Name', null, ['disabled']) !!}
                {!! form_admin()->text('slug', 'Slug', null, ['disabled']) !!}
                {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier', 'Identifier', null, ['disabled']) : '' !!}
                {!! form_admin()->select('active', 'Active', $actives, null, ['disabled']) !!}
            </div>
            <div id="tab-2" class="tab">
                {!! form_admin()->text('metadata[title]', 'Title', null, ['disabled']) !!}
                {!! form_admin()->text('metadata[subtitle]', 'Subtitle', null, ['disabled']) !!}
                {!! form_admin()->editor('metadata[content]', 'Content', null, ['disabled']) !!}
            </div>
            <div id="tab-3" class="tab">
                {!! form_admin()->text('metadata[meta_title]', 'Meta Title', null, ['disabled']) !!}
                {!! form_admin()->textarea('metadata[meta_description]', 'Meta Description', null, ['disabled']) !!}
                {!! form_admin()->textarea('metadata[meta_keywords]', 'Meta Keywords', null, ['disabled']) !!}
            </div>
            {!! block()->container($item, null, $revision, true) !!}
        {!! form()->close() !!}
</section>
@endsection

{!! revision()->view($revision) !!}