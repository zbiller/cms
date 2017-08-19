@extends('layouts::admin.default')

@section('content')

    <div style="text-align: center;">
        <section class="content content-third left">
            <span class="title">Order Totals</span>
            <article class="split">
                <em>Raw Total:</em> {{ number_format($item->raw_total, 2) }} {{ $item->currency }}
            </article>
            <article class="split">
                <em>Sub Total:</em> {{ number_format($item->sub_total, 2) }} {{ $item->currency }}
            </article>
            <article class="split">
                <em>Grand Total:</em> {{ number_format($item->grand_total, 2) }} {{ $item->currency }}
            </article>
        </section>
        <section class="content content-third center">
            <span class="title">Order Details</span>
            <article class="split">
                <em>Order Status:</em> {{ isset($statuses[$item->status]) ? $statuses[$item->status] : 'N/A' }}
            </article>
            <article class="split">
                <em>Payment Method:</em> {{ isset($payments[$item->payment]) ? $payments[$item->payment] : 'N/A' }}
            </article>
            <article class="split">
                <em>Shipping Option:</em> {{ isset($shippings[$item->shipping]) ? $shippings[$item->shipping] : 'N/A' }}
            </article>
        </section>
        <section class="content content-third right">
            <span class="title">Customer Info</span>
            <article class="split">
                <em>Full Name:</em> {{ $item->full_name ?: 'N/A' }}
            </article>
            <article class="split">
                <em>Email Address:</em> {{ $item->email ?: 'N/A' }}
            </article>
            <article class="split">
                <em>Phone Number:</em> {{ $item->phone ?: 'N/A' }}
            </article>
        </section>
    </div>

    <div style="margin-top: 20px;">
        <section class="content content-half left">
            <span class="title">Shipping Address</span>
            {{ $item->shipping_address && isset($item->shipping_address->country) ? $item->shipping_address->country . ', ' : '' }}
            {{ $item->shipping_address && isset($item->shipping_address->state) ? $item->shipping_address->state . ', ' : '' }}
            {{ $item->shipping_address && isset($item->shipping_address->city) ? $item->shipping_address->city : '' }}
            {!! $item->shipping_address->address && isset($item->shipping_address->address) ? '<br />' . nl2br($item->shipping_address->address) : '' !!}
        </section>
        <section class="content content-half right">
            <span class="title">Billing Address</span>
            {{ $item->billing_address && isset($item->billing_address->country) ? $item->billing_address->country . ', ' : '' }}
            {{ $item->billing_address && isset($item->billing_address->state) ? $item->billing_address->state . ', ' : '' }}
            {{ $item->billing_address && isset($item->billing_address->city) ? $item->billing_address->city : '' }}
            {!! $item->billing_address->address && isset($item->billing_address->address) ? '<br />' . nl2br($item->billing_address->address) : '' !!}
        </section>
    </div>

    <section class="list" style="margin-top: 20px; padding: 2px 0 0 0;">
        <table cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <td>Name</td>
                    <td>Quantity</td>
                    <td>Price</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                @if($item->items->count() > 0)
                    @foreach($item->items as $index => $_item)
                        <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                            <td>{{ $_item->name ?: 'N/A' }}</td>
                            <td>{{ $_item->quantity ?: 'N/A' }}</td>
                            <td>{{ number_format($_item->grand_price, 2) . ' ' . ($_item->currency ?: config('shop.price.default_currency')) }}</td>
                            <td>{{ number_format($_item->grand_total, 2) . ' ' . ($_item->currency ?: config('shop.price.default_currency')) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">No products in this order</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->goBack(route('admin.orders.index')) !!}
    </section>
    <section class="actions">
        {!! button()->action('Modify', route('admin.orders.edit', $item->id), 'fa-pencil', 'green') !!}
    </section>
@endsection