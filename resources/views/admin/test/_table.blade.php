<table cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="name">
            <i class="fa fa-sort"></i>&nbsp; Name
        </td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->name }}</td>
                <td>
                    {!! button()->edit('admin.test.edit', ['id' => $item->id]) !!}
                    {!! button()->delete('admin.test.destroy', ['id' => $item->id]) !!}
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