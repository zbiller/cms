{!! validation('admin')->errors() !!}

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

@section('bottom_scripts')
    {{--{!! JsValidator::formRequest(App\Http\Requests\PageRequest::class, '.form') !!}--}}
@append