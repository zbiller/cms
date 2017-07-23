<section class="actions left">
    {!! button()->cancelAction(route('admin.blocks.index')) !!}
</section>
<section class="actions">
    @if($item->exists)
        {!! button()->duplicateRecord(route('admin.blocks.duplicate', $item->id)) !!}
    @endif

    {!! button()->saveAsDraft(route('admin.drafts.save')) !!}
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>