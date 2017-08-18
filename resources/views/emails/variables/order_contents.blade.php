<table class="table" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="header-cell" align="left">
                <strong>Product</strong>
            </th>
            <th class="header-cell" align="center">
                <strong>Quantity</strong>
            </th>
            <th class="header-cell" align="center">
                <strong>Price</strong>
            </th>
            <th class="header-cell" align="right">
                <strong>Total</strong>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td align="left" class="content-cell">{{ $item->name }}</td>
                <td align="center" class="content-cell">{{ $item->quantity }}</td>
                <td align="center" class="content-cell">{{ number_format($item->grand_price) }} {{ $item->currency }} </td>
                <td align="right" class="content-cell">{{ number_format($item->grand_total) }} {{ $item->currency }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td align="left" class="footer-cell"><strong>Total</strong></td>
            <td class="footer-cell"></td>
            <td class="footer-cell"></td>
            <td align="right" class="footer-cell">
                <strong>{{ number_format($order->grand_total) }} {{ $order->currency }}</strong>
            </td>
        </tr>
    </tfoot>
</table>