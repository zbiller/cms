<a href="#tab-1">Basic</a>
<a href="#tab-2">Meta</a>

@if($item->exists)
    {!! block()->tab($item) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->tab($item, 'admin.product_categories.draft') !!}
        {!! revision()->tab($item, 'admin.product_categories.revision') !!}
    @endif
@endif