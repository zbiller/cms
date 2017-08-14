@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.orders._filter')
    </section>

    <section class="list">
        <table cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <td class="sortable" data-sort="sku">
                    <i class="fa fa-sort"></i>&nbsp; Identifier
                </td>
                <td class="sortable" data-sort="grand_total">
                    <i class="fa fa-sort"></i>&nbsp; Total
                </td>
                <td class="sortable" data-sort="metadata">
                    <i class="fa fa-sort"></i>&nbsp; Customer
                </td>
                <td class="sortable" data-sort="status">
                    <i class="fa fa-sort"></i>&nbsp; Status
                </td>
                <td class="actions-deleted">Actions</td>
            </tr>
            </thead>
            <tbody>
            @if($items->count() > 0)
                @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->identifier ?: 'N/A' }}</td>
                        <td>{{ $item->grand_total ? number_format($item->grand_total, 2) . ' ' . ($item->currency ?: config('shop.price.default_currency')) : 'N/A' }}</td>
                        <td>{{ $item->full_name ?: 'N/A' }}</td>
                        <td>{{ isset($status[$item->status]) ? $statuses[$item->status] : 'N/A' }}</td>
                        <td>
                            {!! button()->restoreRecord(route('admin.orders.restore', $item->id)) !!}
                            {!! button()->deleteRecord(route('admin.orders.delete', $item->id)) !!}
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
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->goBack(route('admin.orders.index')) !!}
    </section>
@endsection