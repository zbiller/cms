@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.products._filter')
    </section>

    <section class="list">
        <table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <td class="sortable" data-sort="category_id">
                    <i class="fa fa-sort"></i>&nbsp; Category
                </td>
                <td class="sortable" data-sort="sku">
                    <i class="fa fa-sort"></i>&nbsp; SKU
                </td>
                <td class="sortable" data-sort="name">
                    <i class="fa fa-sort"></i>&nbsp; Name
                </td>
                <td class="sortable" data-sort="price">
                    <i class="fa fa-sort"></i>&nbsp; Price
                </td>
                <td class="sortable" data-sort="quantity">
                    <i class="fa fa-sort"></i>&nbsp; Quantity
                </td>
                <td class="sortable" data-sort="active">
                    <i class="fa fa-sort"></i>&nbsp; Active
                </td>
                <td class="actions-drafted">Actions</td>
            </tr>
            </thead>
            <tbody>
            @if($items->count() > 0)
                @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->category ? $item->category->name : 'N/A' }}</td>
                        <td>{{ $item->sku ?: 'N/A' }}</td>
                        <td>{{ $item->name ?: 'N/A' }}</td>
                        <td>{{ $item->price ? number_format($item->price) . ' ' . $item->currency->code : 'N/A' }}</td>
                        <td>{{ $item->quantity ?: 'N/A' }}</td>
                        <td>{{ $item->active ? 'Yes' : 'No' }}</td>
                        <td>
                            {!! button()->publishLimboDraft(route('admin.drafts.publish_limbo'), $item) !!}
                            {!! button()->editRecord(route('admin.products.limbo', $item->id)) !!}
                            {!! button()->deleteLimboDraft(route('admin.drafts.delete_limbo'), $item) !!}
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

    <section class="right">
        {!! button()->goBack(route('admin.products.index')) !!}
    </section>
@endsection