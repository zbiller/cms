{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('country_id', 'Country', $countries->pluck('name', 'id')) !!}
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('code') !!}
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\CityRequest::class, '.form') !!}
@append