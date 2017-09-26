<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="created_at">
                <i class="fa fa-sort"></i>&nbsp; Logged At
            </td>
            <td class="actions-activity-logs">Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->name ?: 'N/A' }}</td>
                    <td>{{ $item->created_at ?: 'N/A' }}</td>
                    <td class="actions-activity-logs">
                        {!! button()->deleteRecord(route('admin.activity.destroy', $item->id), ['style' => 'float: right; margin-left: 0; margin-right: 5px;']) !!}
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