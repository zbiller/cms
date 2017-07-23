<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Details</a>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->tab($item, 'admin.blocks.draft') !!}
    {!! revision()->tab($item, 'admin.blocks.revision') !!}
@endif