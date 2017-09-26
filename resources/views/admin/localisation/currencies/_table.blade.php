<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="code">
                <i class="fa fa-sort"></i>&nbsp; Code
            </td>
            <td class="sortable" data-sort="symbol">
                <i class="fa fa-sort"></i>&nbsp; Symbol
            </td>
            <td class="sortable" data-sort="exchange_rate">
                <i class="fa fa-sort"></i>&nbsp; Exchange Rate
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->name ?: 'N/A' }}</td>
                    <td>{{ $item->code ?: 'N/A' }}</td>
                    <td>{{ $item->symbol ?: 'N/A' }}</td>
                    <td>{{ $item->exchange_rate ? number_format($item->exchange_rate, 4) : 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.currencies.edit', $item->id)) !!}
                        {!! button()->deleteRecord(route('admin.currencies.destroy', $item->id)) !!}
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