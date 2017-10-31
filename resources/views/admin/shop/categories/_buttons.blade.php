<section class="left">
    {!! button()->cancelAction(route('admin.product_categories.index')) !!}
</section>
<section class="right">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.product_categories.duplicate', $item->id)) !!}
    @endif

    {!! button()->previewRecord(route('admin.product_categories.preview', $item->id)) !!}
    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>