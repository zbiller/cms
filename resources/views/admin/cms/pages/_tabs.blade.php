<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Content</a>
<a href="#tab-3">Meta Tags</a>

@if($item->exists)
    {!! block()->tab($item) !!}
    {!! draft()->tab($item, 'admin.pages.draft') !!}
    {!! revision()->tab($item, 'admin.pages.revision') !!}
@endif