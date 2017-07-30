<section class="actions left">
    {!! button()->cancelAction(route('admin.categories.index')) !!}
</section>
<section class="actions">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.categories.duplicate', $item->id)) !!}
    @endif

    {!! button()->previewRecord(route('admin.categories.preview', $item->id)) !!}
    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>