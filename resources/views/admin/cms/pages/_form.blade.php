{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Cms\Page::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\PageRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}

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

    <script type="text/javascript">
        var type = $('select[name="type"]');
        var getLayouts = function (_this) {
            var url = '{{ route('admin.pages.get_layouts') }}' + '/' + type.val();
            var select = $('select[name="layout_id"]');

            $.ajax({
                type : 'GET',
                url: url,
                success : function(data) {
                    if (data.status === true) {
                        select.empty();

                        $.each(data.layouts, function (id, name) {
                            select.append('<option value="' + id + '">' + name + '</option>');
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