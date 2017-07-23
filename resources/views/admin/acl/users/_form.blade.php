@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('username', 'Username') !!}
    {!! form_admin()->password('password', 'Password', ['placeholder' => 'Leave blank to remain the same'], true) !!}
    {!! form_admin()->password('password_confirmation', 'Confirm Password') !!}
    {!! form_admin()->select('verified', 'Verified', $verified) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->text('person[first_name]', 'First Name') !!}
    {!! form_admin()->text('person[last_name]', 'Last Name') !!}
    {!! form_admin()->text('person[email]', 'Email') !!}
    {!! form_admin()->text('person[phone]', 'Phone') !!}
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\UserRequest::class, '.form') !!}
@append