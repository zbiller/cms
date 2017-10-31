<section class="left">
    {!! button()->cancelAction(route('admin.addresses.index', $user->id)) !!}
</section>
<section class="right">
    {!! button()->saveAndStay() !!}
    {!! button()->saveRecord() !!}
</section>