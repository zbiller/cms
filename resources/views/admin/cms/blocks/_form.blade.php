{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Cms\Block::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\BlockRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('anchor') !!}
</div>
<div id="tab-2" class="tab">
    @include('blocks_' . ($item->exists ? $item->type : $type) . '::admin')
</div>

@if($item->exists)
    {!! draft()->container($item) !!}
    {!! revision()->container($item) !!}
@endif

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\BlockRequest::class, '.form') !!}
@append