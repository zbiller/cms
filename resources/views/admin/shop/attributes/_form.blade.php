@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}
{!! form()->hidden('set_id', $set->id) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
    {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
    {!! form_admin()->select('type', 'Type', $types) !!}
    <div id="type-{{ \App\Models\Shop\Attribute::TYPE_TEXT }}" class="attribute-value">
        {!! form_admin()->textarea('value', 'Value') !!}
    </div>
    <div id="type-{{ \App\Models\Shop\Attribute::TYPE_FILE }}" class="attribute-value">
        {!! uploader()->field('value')->label('Value')->model($item)->manager() !!}
    </div>
    <div id="type-{{ \App\Models\Shop\Attribute::TYPE_EDITOR }}" class="attribute-value">
        {!! form_admin()->editor('value', 'Value') !!}
    </div>
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\AttributeRequest::class, '.form') !!}

    <script type="text/javascript">
        @if($item->exists)
            var type = '{{ $item->type }}';
        @else
            var type = $('select[name="type"]').val() ? $('select[name="type"]').val() : '{{ \App\Models\Shop\Attribute::TYPE_TEXT }}';
        @endif

        var switchType = function (type) {
            $('.attribute-value').hide()
                .find('input[name="value"], textarea[name="value"], select[name="value"]').attr('name', 'dummy-value');

            $('#type-' + type).show()
                .find('input[name="dummy-value"], textarea[name="dummy-value"], select[name="dummy-value"]').attr('name', 'value');
        };

        switchType(type);

        $('select[name="type"]').change(function () {
            switchType($(this).val());
        });
    </script>
@append