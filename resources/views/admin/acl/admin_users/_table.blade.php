<table cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="username">
            <i class="fa fa-sort"></i>&nbsp; Username
        </td>
        <td class="sortable" data-sort="person.first_name">
            <i class="fa fa-sort"></i>&nbsp; First Name
        </td>
        <td class="sortable" data-sort="person.last_name">
            <i class="fa fa-sort"></i>&nbsp; Last Name
        </td>
        <td class="sortable" data-sort="person.email">
            <i class="fa fa-sort"></i>&nbsp; Email
        </td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->username ?: 'N/A' }}</td>
                <td>{{ $item->first_name ?: 'N/A' }}</td>
                <td>{{ $item->last_name ?: 'N/A' }}</td>
                <td>{{ $item->email ?: 'N/A' }}</td>
                <td>
                    {!! button()->editRecord(route('admin.admin_users.edit', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.admin_users.destroy', $item->id)) !!}
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