<section class="actions left">
    {!! button()->cancelAction(route('admin.attributes.index', $set)) !!}
</section>
<section class="actions">
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>