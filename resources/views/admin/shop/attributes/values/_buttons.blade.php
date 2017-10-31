<section class="left">
    {!! button()->cancelAction(route('admin.attribute_values.index', ['set' => $set, 'attribute' => $attribute])) !!}
</section>
<section class="right">
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>