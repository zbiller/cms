{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
    {!! form_admin()->select('type', 'Type', $types) !!}
</div>

@if($item->exists)
    {!! block()->containers($item) !!}
@endif

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\LayoutRequest::class, '.form') !!}
@append