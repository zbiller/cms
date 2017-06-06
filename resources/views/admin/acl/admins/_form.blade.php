{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('roles[]', 'Roles', $roles->pluck('name', 'id'), $item->exists ? $item->roles->pluck('id') : null, ['multiple']) !!}
    {!! form_admin()->text('username', 'Username') !!}
    {!! form_admin()->password('password', 'Password', ['placeholder' => 'Leave blank to remain the same'], true) !!}
    {!! form_admin()->password('password_confirmation', 'Confirm Password') !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->text('person[first_name]', 'First Name') !!}
    {!! form_admin()->text('person[last_name]', 'Last Name') !!}
    {!! form_admin()->text('person[email]', 'Email') !!}
    {!! form_admin()->text('person[phone]', 'Phone') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\AdminRequest::class, '.form') !!}
@append