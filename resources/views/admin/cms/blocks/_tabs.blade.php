<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Details</a>

@if($item->exists)
    {!! draft()->tab($item, 'admin.blocks.draft') !!}
    {!! revision()->tab($item, 'admin.blocks.revision') !!}
@endif