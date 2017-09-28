<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="old_url">
                <i class="fa fa-sort"></i>&nbsp; Old Url
            </td>
            <td class="sortable" data-sort="new_url">
                <i class="fa fa-sort"></i>&nbsp; New Url
            </td>
            <td class="sortable" data-sort="status">
                <i class="fa fa-sort"></i>&nbsp; Type
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->old_url ?: 'N/A' }}</td>
                <td>{{ $item->new_url ?: 'N/A' }}</td>
                <td>{{ $statuses[$item->status] ?? 'N/A' }}</td>
                <td>
                    {!! button()->editRecord(route('admin.redirects.edit', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.redirects.destroy', $item->id)) !!}
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