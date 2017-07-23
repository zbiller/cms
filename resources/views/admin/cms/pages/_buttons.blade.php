<section class="actions left">
    {!! button()->cancelAction(route('admin.pages.index')) !!}
</section>
<section class="actions">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.pages.duplicate', $item->id)) !!}
    @endif

    {!! button()->previewRecord(route('admin.pages.preview', $item->id)) !!}
    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>