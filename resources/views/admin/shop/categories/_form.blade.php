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

{!! form()->hidden('_class', \App\Models\Shop\Category::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\Shop\CategoryRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.product_categories.drafts')) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name', 'Name', null, $item->exists ? [] : ['id' => 'slug-from']) !!}
    {!! form_admin()->text('slug', 'Slug', null, $item->exists ? [] : ['id' => 'slug-to']) !!}
    {!! form_admin()->select('active', 'Active', $actives) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->text('metadata[meta][title]', 'Title') !!}
    {!! uploader()->field('metadata[meta][image]')->label('Image')->model($item)->types('image')->manager() !!}
    {!! form_admin()->textarea('metadata[meta][description]', 'Description') !!}
    {!! form_admin()->textarea('metadata[meta][keywords]', 'Keywords') !!}
</div>
<div id="tab-3" class="tab tab-discounts">
    @include('admin.shop.categories.discounts.assign', ['item' => $item, 'draft' => isset($draft) ? $draft : null, 'revision' => isset($revision) ? $revision : null, 'disabled' => isset($on_revision) ? true : false])
</div>
<div id="tab-4" class="tab tab-taxes">
    @include('admin.shop.categories.taxes.assign', ['item' => $item, 'draft' => isset($draft) ? $draft : null, 'revision' => isset($revision) ? $revision : null, 'disabled' => isset($on_revision) ? true : false])
</div>

@if($item->exists)
    {!! block()->container($item, isset($on_draft) ? $draft : null, isset($on_revision) ? $revision : null, isset($on_revision) ? true : false) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->container($item) !!}
        {!! revision()->container($item) !!}
    @endif
@endif

{!! form_admin()->close() !!}

@if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    @section('bottom_scripts')
        {!! JsValidator::formRequest(App\Http\Requests\Shop\CategoryRequest::class, '.form') !!}
    @append
@endif