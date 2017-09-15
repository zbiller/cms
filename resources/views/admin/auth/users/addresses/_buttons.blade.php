<section class="actions left">
    {!! button()->cancelAction(route('admin.addresses.index', $user->id)) !!}
</section>
<section class="actions">
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>