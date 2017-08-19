<a href="#tab-1">Basic</a>
<a href="#tab-2">Content</a>
<a href="#tab-3">Meta</a>
<a href="#tab-4">Images</a>
<a href="#tab-5">Attributes</a>
<a href="#tab-6">Discounts</a>
<a href="#tab-7">Taxes</a>

@if($item->exists)
    {!! block()->tab($item) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->tab($item, 'admin.products.draft') !!}
        {!! revision()->tab($item, 'admin.products.revision') !!}
    @endif
@endif