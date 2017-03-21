{!! validation()->errors() !!}

<div id="tab-1" class="tab">
    {!! adminform()->text('name') !!}
    {!! adminform()->file('file') !!}
    {!! adminform()->file('image') !!}
    {!! adminform()->file('video') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Crud\AdminRoleRequest::class, '.form') !!}
@append