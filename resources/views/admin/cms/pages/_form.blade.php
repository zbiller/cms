{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Cms\Page::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\PageRequest::class) !!}
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

@if($item->exists)
    {!! block()->container($item) !!}
    {!! draft()->container($item) !!}
    {!! revision()->container($item) !!}
@endif

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\PageRequest::class, '.form') !!}
@append