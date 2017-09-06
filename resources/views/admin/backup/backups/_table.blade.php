<table cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="name">
            <i class="fa fa-sort"></i>&nbsp; Name
        </td>
        <td class="sortable" data-sort="date">
            <i class="fa fa-sort"></i>&nbsp; Date
        </td>
        <td class="sortable" data-sort="size">
            <i class="fa fa-sort"></i>&nbsp; Size
        </td>
        <td class="actions-backups">Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->name ?: 'N/A' }}</td>
                <td>{{ $item->date ?: 'N/A' }}</td>
                <td>{{ $item->size_in_mb . ' MB' }}</td>
                <td>
                    {!! button()->downloadFile(route('admin.backups.download', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.backups.destroy', $item->id)) !!}
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