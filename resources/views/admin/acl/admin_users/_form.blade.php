{!! validation()->errors() !!}

<div id="tab-1" class="tab">
    {!! adminform()->select('roles[]', 'Roles', $roles->pluck('name', 'id'), $item->id ? $item->roles->pluck('id') : null, ['multiple']) !!}
    {!! adminform()->text('username', 'Username') !!}
    {!! adminform()->password('password', 'Password', ['placeholder' => 'Leave blank to remain the same'], true) !!}
    {!! adminform()->password('password_confirmation', 'Confirm Password') !!}
</div>
<div id="tab-2" class="tab">
    {!! adminform()->text('person[first_name]', 'First Name') !!}
    {!! adminform()->text('person[last_name]', 'Last Name') !!}
    {!! adminform()->text('person[email]', 'Email') !!}
    {!! adminform()->text('person[phone]', 'Phone') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest($item->id ? App\Http\Requests\Crud\AdminUserWithoutPasswordRequest::class : App\Http\Requests\Crud\AdminUserRequest::class, '.form') !!}
@append