<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="country_id">
                <i class="fa fa-sort"></i>&nbsp; Country
            </td>
            <td class="sortable" data-sort="state_id">
                <i class="fa fa-sort"></i>&nbsp; State
            </td>
            <td class="sortable" data-sort="city_id">
                <i class="fa fa-sort"></i>&nbsp; City
            </td>
            <td class="sortable" data-sort="address">
                <i class="fa fa-sort"></i>&nbsp; Address
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->country && $item->country->exists ? $item->country->name : 'N/A' }}</td>
                    <td>{{ $item->state && $item->state->exists ? $item->state->name : 'N/A' }}</td>
                    <td>{{ $item->city && $item->city->exists ? $item->city->name : 'N/A' }}</td>
                    <td>{{ $item->address ? str_limit($item->address, 60) : 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.addresses.edit', ['user' => $user->id, 'id' => $item->id])) !!}
                        {!! button()->deleteRecord(route('admin.addresses.destroy', ['user' => $user->id, 'id' => $item->id])) !!}
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