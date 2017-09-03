@if($item->exists)
    @if(isset($on_draft) || isset($on_limbo_draft) || isset($on_revision))
        {!! form_admin()->model($item, ['method' => isset($on_draft) || isset($on_revision) ? 'POST' : 'PUT','class' => 'form', 'files' => true]) !!}
    @else
        {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
    @endif
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Shop\Order::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\Shop\OrderRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.orders.drafts')) !!}

<div id="tab-1" class="tab">
    <div class="box warning" style="margin-bottom: 20px;">
        <span>
            Please note that all of the order's totals are dynamically generated using the assigned products and their quantities.
        </span>
    </div>
    {!! form_admin()->text('', 'Raw Total', $item && $item->exists ? number_format($item->raw_total, 2) . ' ' . $item->currency : '0.00 ' . config('shop.price.default_currency'), ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('', 'Sub Total', $item && $item->exists ? number_format($item->sub_total, 2) . ' ' . $item->currency : '0.00 ' . config('shop.price.default_currency'), ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('', 'Grand Total', $item && $item->exists ? number_format($item->grand_total, 2) . ' ' . $item->currency : '0.00 ' . config('shop.price.default_currency'), ['disabled' => 'disabled']) !!}
    {!! form_admin()->text('identifier') !!}
    {!! form_admin()->select('status', 'Status', $statuses) !!}
    {!! form_admin()->select('payment', 'Payment Type', $payments) !!}
    {!! form_admin()->select('shipping', 'Shipping Type', $shippings) !!}
</div>
<div id="tab-2" class="tab">
    {!! form_admin()->text('customer[first_name]', 'First Name') !!}
    {!! form_admin()->text('customer[last_name]', 'Last Name') !!}
    {!! form_admin()->text('customer[email]', 'Email') !!}
    {!! form_admin()->text('customer[phone]', 'Phone') !!}
</div>
<div id="tab-3" class="tab">
    <span class="title">Shipping</span>
    {!! form_admin()->text('addresses[shipping][country]', 'Country') !!}
    {!! form_admin()->text('addresses[shipping][state]', 'State') !!}
    {!! form_admin()->text('addresses[shipping][city]', 'City') !!}
    {!! form_admin()->textarea('addresses[shipping][address]', 'Address') !!}

    <span class="title">Delivery</span>
    {!! form_admin()->text('addresses[billing][country]', 'Country') !!}
    {!! form_admin()->text('addresses[billing][state]', 'State') !!}
    {!! form_admin()->text('addresses[billing][city]', 'City') !!}
    {!! form_admin()->textarea('addresses[billing][address]', 'Address') !!}
</div>
<div id="tab-4" class="tab">
    @include('admin.shop.orders.items.assign', ['item' => $item, 'draft' => $draft ?? null, 'revision' => $revision ?? null, 'disabled' => isset($on_revision) ? true : false])
</div>

{!! form_admin()->close() !!}

@section('bottom_scripts')
    @if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
        {!! JsValidator::formRequest(App\Http\Requests\Shop\OrderRequest::class, '.form') !!}
    @endif
@append