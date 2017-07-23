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

{!! form()->hidden('_class', \App\Models\Cms\Block::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\BlockRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.blocks.drafts')) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('anchor') !!}
</div>
<div id="tab-2" class="tab">
    @include('blocks_' . ($item->exists ? $item->type : $type) . '::admin')
</div>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->container($item) !!}
    {!! revision()->container($item) !!}
@endif

{!! form_admin()->close() !!}

@if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    @section('bottom_scripts')
        {!! JsValidator::formRequest(App\Http\Requests\BlockRequest::class, '.form') !!}
    @append
@endif