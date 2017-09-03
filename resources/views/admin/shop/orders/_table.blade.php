<table cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="sku">
                <i class="fa fa-sort"></i>&nbsp; Identifier
            </td>
            <td class="sortable" data-sort="grand_total">
                <i class="fa fa-sort"></i>&nbsp; Total
            </td>
            <td class="sortable" data-sort="customer">
                <i class="fa fa-sort"></i>&nbsp; Customer
            </td>
            <td class="sortable" data-sort="status">
                <i class="fa fa-sort"></i>&nbsp; Status
            </td>
            <td class="sortable" data-sort="viewed">
                <i class="fa fa-sort"></i>&nbsp; Viewed
            </td>
            <td class="actions-orders">Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->identifier ?: 'N/A' }}</td>
                    <td>{{ number_format($item->grand_total, 2) . ' ' . ($item->currency ?: config('shop.price.default_currency')) }}</td>
                    <td>{{ $item->full_name ?: 'N/A' }}</td>
                    <td>{{ $statuses[$item->status] ?? 'N/A' }}</td>
                    <td>
                        <span class="flag {!! $item->viewed == \App\Models\Shop\Order::VIEWED_NO ? 'red' : 'green' !!}">
                            {!! $item->viewed == \App\Models\Shop\Order::VIEWED_NO ? 'No' : 'Yes' !!}
                        </span>
                    </td>
                    <td>
                        {!! button()->editRecord(route('admin.orders.edit', $item->id)) !!}
                        {!! button()->viewRecord(route('admin.orders.view', $item->id)) !!}
                        {!! button()->deleteRecord(route('admin.orders.destroy', $item->id)) !!}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10">No records found</td>
            </tr>
        @endif
    </tbody>
</table>