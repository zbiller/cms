<a href="#tab-1">Primary Information</a>
<a href="#tab-2">Manage Details</a>
<a href="#tab-3">Variables</a>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->tab($item, 'admin.emails.draft') !!}
    {!! revision()->tab($item, 'admin.emails.revision') !!}
@endif