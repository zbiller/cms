@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('locale', 'Language', null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('group', 'Group', null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('key', 'Key', null, ['disabled' => 'disabled']) !!}
    {!! form_admin()->textarea('value', 'Value') !!}
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\Translation\TranslationRequest::class, '.form') !!}
@append