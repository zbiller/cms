<table class="table" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="header-cell" align="left">
                <strong>Product</strong>
            </th>
            <th class="header-cell" align="center">
                <strong>Quantity</strong>
            </th>
            <th class="header-cell" align="right">
                <strong>Price</strong>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($cart->items as $item)
            <tr>
                <td align="left" class="content-cell">
                    {{ $item->product->name }}
                </td>
                <td align="center" class="content-cell">
                    {{ $item->quantity }}
                </td>
                <td align="right" class="content-cell">
                    {{ number_format($item->quantity * \App\Models\Localisation\Currency::convert($item->product->final_price, $item->product->currency->code, config('shop.price.default_currency'))) }}&nbsp;
                    {{ config('shop.price.default_currency') }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td align="left" class="footer-cell"><strong>Total</strong></td>
            <td class="footer-cell"></td>
            <td align="right" class="footer-cell">
                <strong>{{ number_format($cart->grand_total) }} {{ config('shop.price.default_currency') }}</strong>
            </td>
        </tr>
    </tfoot>
</table>