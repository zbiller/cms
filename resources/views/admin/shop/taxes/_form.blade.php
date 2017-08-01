@if($item->exists)
    {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->select('for', 'For', $for) !!}
    {!! form_admin()->text('name') !!}
    {!! form_admin()->text('rate') !!}
    {!! form_admin()->select('type', 'Type', $types) !!}
    {!! form_admin()->text('uses', 'Uses') !!}
    {!! form_admin()->select('active', 'Active', $actives) !!}
</div>
<div id="tab-2" class="tab">
    <div class="box notification" style="margin-bottom: 20px;">
        <span>
            This tax will be applied only in the date interval selected
        </span>
    </div>
    {!! form_admin()->calendar('start_date') !!}
    {!! form_admin()->calendar('end_date') !!}
</div>
<div id="tab-3" class="tab">
    <div class="box notification" style="margin-bottom: 20px;">
        <span>
            This tax will be applied only before the maximum value is met.<br />
            The "maximum value" represents either the product's price or the order's total, depending on your discount applicability.
        </span>
    </div>
    {!! form_admin()->text('max_val', 'Maximum Value') !!}
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\TaxRequest::class, '.form') !!}
@append