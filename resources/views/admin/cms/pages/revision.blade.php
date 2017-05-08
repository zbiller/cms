@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Page</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Content</a>
        <a href="#tab-3">Meta Tags</a>
        {!! block()->tabs($item) !!}
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['class' => 'form']) !!}
            <div id="tab-1" class="tab">
                {!! form_admin()->select('layout_id', 'Layout', $layouts->pluck('name', 'id')) !!}
                {!! form_admin()->select('type', 'Type', $types) !!}
                {!! form_admin()->text('name') !!}
                {!! form_admin()->text('slug') !!}
                {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
                {!! form_admin()->select('active', 'Active', $actives) !!}
            </div>
            <div id="tab-2" class="tab">
                {!! form_admin()->text('metadata[title]', 'Title') !!}
                {!! form_admin()->text('metadata[subtitle]', 'Subtitle') !!}
                {!! form_admin()->editor('metadata[content]', 'Content') !!}
            </div>
            <div id="tab-3" class="tab">
                {!! form_admin()->text('metadata[meta_title]', 'Meta Title') !!}
                {!! form_admin()->textarea('metadata[meta_description]', 'Meta Description') !!}
                {!! form_admin()->textarea('metadata[meta_keywords]', 'Meta Keywords') !!}
            </div>
            {!! block()->containers($item, true) !!}
        {!! form()->close() !!}
</section>
@endsection

{!! revision()->view($revision) !!}

