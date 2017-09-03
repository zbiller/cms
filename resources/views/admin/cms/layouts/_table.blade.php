<table cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="type">
                <i class="fa fa-sort"></i>&nbsp; Type
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->name ?: 'N/A' }}</td>
                <td>{{ $types[$item->type] ?? 'N/A' }}</td>
                <td>
                    {!! button()->editRecord(route('admin.layouts.edit', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.layouts.destroy', $item->id)) !!}
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