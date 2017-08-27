@if($item->exists)
    @if(isset($on_draft) || isset($on_limbo_draft) || isset($on_revision))
        {!! form_admin()->model($item, ['method' => isset($on_draft) || isset($on_revision) ? 'POST' : 'PUT','class' => 'form', 'files' => true]) !!}
    @else
        {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
    @endif
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Cms\Page::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\Cms\PageRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.pages.drafts')) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('type', 'Type', $types) !!}
    {!! form_admin()->select('layout_id', 'Layout', $item->exists && isset($layouts) ? $layouts->pluck('name', 'id') : []) !!}
    {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
    {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
    {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
    {!! form_admin()->text('canonical', 'Canonical') !!}
    {!! form_admin()->select('active', 'Active', $actives) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->text('metadata[title]', 'Title') !!}
    {!! form_admin()->text('metadata[subtitle]', 'Subtitle') !!}
    {!! form_admin()->editor('metadata[content]', 'Content') !!}
</div>
<div id="tab-3" class="tab">
    {!! form_admin()->text('metadata[meta][title]', 'Title') !!}
    {!! uploader()->field('metadata[meta][image]')->label('Image')->model($item)->manager() !!}
    {!! form_admin()->textarea('metadata[meta][description]', 'Description') !!}
    {!! form_admin()->textarea('metadata[meta][keywords]', 'Keywords') !!}
</div>

@if($item->exists)
    {!! block()->container($item, isset($on_draft) ? $draft : null, isset($on_revision) ? $revision : null, isset($on_revision) ? true : false) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->container($item) !!}
        {!! revision()->container($item) !!}
    @endif
@endif

{!! form_admin()->close() !!}

@section('bottom_scripts')
    <script type="text/javascript">
        var type = $('select[name="type"]');
        var getLayouts = function (_this) {
            var url = '{{ route('admin.pages.layouts') }}' + '/' + type.val();
            var select = $('select[name="layout_id"]');

            $.ajax({
                type : 'GET',
                url: url,
                success : function(data) {
                    if (data.status === true) {
                        select.empty();

                        $.each(data.items, function (index, item) {
                            select.append('<option value="' + item.id + '">' + item.name + '</option>');
                        });

                        select.trigger("chosen:updated");
                    }
                }
            });
        };

        if (type.length) {
            @if(!$item->exists)
                if (type.val()) {
                getLayouts();
            }
            @endif

            type.change(function () {
                getLayouts();
            });
        }
    </script>
@append

@if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    @section('bottom_scripts')
        {!! JsValidator::formRequest(App\Http\Requests\Cms\PageRequest::class, '.form') !!}
    @append
@endif