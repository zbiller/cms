<a href="#tab-1">Basic</a>
<a href="#tab-2">Details</a>
<a href="#tab-3">Variables</a>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->tab($item, 'admin.emails.draft') !!}
    {!! revision()->tab($item, 'admin.emails.revision') !!}
@endif