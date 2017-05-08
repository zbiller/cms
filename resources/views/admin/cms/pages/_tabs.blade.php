<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Content</a>
<a href="#tab-3">Meta Tags</a>

@if($item->exists)
    {!! block()->tabs($item) !!}
@endif

@if($item->exists)
    {!! revision()->tab($item, 'admin.pages.revision') !!}
@endif