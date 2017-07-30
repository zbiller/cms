<table cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="rate">
                <i class="fa fa-sort"></i>&nbsp; Rate
            </td>
            <td class="sortable" data-sort="type">
                <i class="fa fa-sort"></i>&nbsp; Type
            </td>
            <td class="sortable" data-sort="for">
                <i class="fa fa-sort"></i>&nbsp; For
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
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->name ?: 'N/A' }}</td>
                    <td>{{ $item->rate ? number_format($item->rate) : 'N/A' }}</td>
                    <td>{{ isset($types[$item->type]) ? $types[$item->type] : 'N/A' }}</td>
                    <td>{{ isset($for[$item->for]) ? $for[$item->for] : 'N/A' }}</td>
                    <td>{{ isset($actives[$item->active]) ? $actives[$item->active] : 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.discounts.edit', $item->id)) !!}
                        {!! button()->deleteRecord(route('admin.discounts.destroy', $item->id)) !!}
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