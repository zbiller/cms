<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Content</a>
<a href="#tab-3">Meta Tags</a>
<a href="#tab-4">Images</a>

@if($item->exists)
    <a href="#tab-5">Discounts</a>

    {!! block()->tab($item) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->tab($item, 'admin.products.draft') !!}
        {!! revision()->tab($item, 'admin.products.revision') !!}
    @endif
@endif