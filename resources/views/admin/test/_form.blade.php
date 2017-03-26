{!! validation()->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->file('image') !!}
    {!! form_admin()->file('video') !!}
    {!! form_admin()->file('audio') !!}
    {!! form_admin()->file('file') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Crud\TestRequest::class, '.form') !!}
@append