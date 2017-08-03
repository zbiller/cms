<section class="actions left">
    {!! button()->cancelAction(route('admin.values.index', ['set' => $set, 'attribute' => $attribute])) !!}
</section>
<section class="actions">
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>