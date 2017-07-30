<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Meta Tags</a>

@if($item->exists)
    {!! block()->tab($item) !!}

    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! draft()->tab($item, 'admin.categories.draft') !!}
        {!! revision()->tab($item, 'admin.categories.revision') !!}
    @endif
@endif