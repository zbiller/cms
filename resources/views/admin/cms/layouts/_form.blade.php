{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('identifier') !!}
    {!! form_admin()->select('file', 'File', $files) !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\LayoutRequest::class, '.form') !!}
@append