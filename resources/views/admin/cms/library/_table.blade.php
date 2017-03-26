<table cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="id">
            <i class="fa fa-sort"></i>&nbsp; #
        </td>
        <td class="sortable" data-sort="original_name">
            <i class="fa fa-sort"></i>&nbsp; Name
        </td>
        <td class="sortable" data-sort="type">
            <i class="fa fa-sort"></i>&nbsp; Type
        </td>
        <td class="sortable" data-sort="size">
            <i class="fa fa-sort"></i>&nbsp; Size
        </td>
        <td class="actions-big">Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td><img src="{{ $item->thumbnail() }}" /></td>
                <td>{{ $item->original_name }}</td>
                <td>{{ $types[$item->type] }}</td>
                <td>{{ number_format($item->size / pow(1024, 2), 2) . ' MB' }}</td>
                <td>
                    {!! button()->download('admin.library.download', ['id' => $item->id]) !!}
                    {!! button()->delete('admin.library.destroy', ['id' => $item->id]) !!}
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