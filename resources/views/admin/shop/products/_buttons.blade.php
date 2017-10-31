<section class="left">
    {!! button()->cancelAction(route('admin.products.index')) !!}
</section>
<section class="right">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.products.duplicate', $item->id)) !!}
    @endif

    {!! button()->previewRecord(route('admin.products.preview', $item->id)) !!}
    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>