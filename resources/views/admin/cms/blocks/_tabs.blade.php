<a href="#tab-1">Basic</a>
<a href="#tab-2">Details</a>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->tab($item, 'admin.blocks.draft') !!}
    {!! revision()->tab($item, 'admin.blocks.revision') !!}
@endif