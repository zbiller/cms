@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        <a href="#tab-1">Primary Information</a>
        <a href="#tab-2">Manage Content</a>
        <a href="#tab-3">Meta Tags</a>
        {!! block()->tab($item) !!}
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['method' => 'PUT', 'class' => 'form']) !!}

        {!! form()->hidden('_back', route('admin.pages.drafts')) !!}
        {!! form()->hidden('_class', \App\Models\Cms\Page::class) !!}
        {!! form()->hidden('_id', $item->exists ? $item->id : null) !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->select('layout_id', 'Layout', $layouts->pluck('name', 'id')) !!}
            {!! form_admin()->select('type', 'Type', $types) !!}
            {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
            {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
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
        {!! block()->container($item) !!}
        {!! form()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.pages.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}
        {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
    </section>
@endsection

