<table class="table" cellspacing="0" cellpadding="0" border="0">
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
        <td class="actions-users">Actions</td>
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
                    {!! form()->open(['url' => route('admin.users.impersonate', $item->id), 'method' => 'POST', 'target' => '_blank']) !!}
                    {!! form()->button('<i class="fa fa-user"></i>&nbsp; Impersonate', ['type' => 'submit', 'class' => 'btn yellow no-margin-top no-margin-bottom no-margin-left double-margin-right']) !!}
                    {!! form()->close() !!}

                    {!! button()->editRecord(route('admin.users.edit', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.users.destroy', $item->id)) !!}
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