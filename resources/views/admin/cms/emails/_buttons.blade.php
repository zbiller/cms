<section class="actions left">
    {!! button()->cancelAction(route('admin.emails.index')) !!}
</section>
<section class="actions">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.emails.duplicate', $item->id)) !!}
    @endif

    {!! button()->previewRecord(route('admin.emails.preview', $item->id)) !!}
    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>