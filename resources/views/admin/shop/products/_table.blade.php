<table
    cellspacing="0" cellpadding="0" border="0"
    data-orderable="{{ $orderable ? 'true' : 'false' }}"
    data-order-url="{{ route('admin.products.order') }}"
    data-order-model="{{ \App\Models\Shop\Product::class }}"
    data-order-token="{{ csrf_token() }}"
>
    <thead>
        <tr class="nodrag nodrop">
            <td class="sortable" data-sort="sku">
                <i class="fa fa-sort"></i>&nbsp; SKU
            </td>
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="category_id">
                <i class="fa fa-sort"></i>&nbsp; Category
            </td>
            <td class="sortable" data-sort="price">
                <i class="fa fa-sort"></i>&nbsp; Price
            </td>
            <td class="sortable" data-sort="price">
                <i class="fa fa-sort"></i>&nbsp; Total
            </td>
            <td class="sortable" data-sort="quantity">
                <i class="fa fa-sort"></i>&nbsp; Quantity
            </td>
            <td class="sortable" data-sort="active">
                <i class="fa fa-sort"></i>&nbsp; Active
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr id="{{ $item->id }}" class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->sku ?: 'N/A' }}</td>
                    <td>{{ $item->name ?: 'N/A' }}</td>
                    <td>{{ $item->category ? $item->category->name : 'N/A' }}</td>
                    <td>{{ $item->price ? number_format($item->price) . ' ' . $item->currency->code : 'N/A' }}</td>
                    <td>{{ $item->final_price ? number_format($item->final_price) . ' ' . $item->currency->code : 'N/A' }}</td>
                    <td>{{ $item->quantity ?: 'N/A' }}</td>
                    <td>{{ isset($actives[$item->active]) ? $actives[$item->active] : 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.products.edit', $item->id)) !!}
                        {!! button()->deleteRecord(route('admin.products.destroy', $item->id)) !!}
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