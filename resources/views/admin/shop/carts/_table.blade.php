<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="user_id">
                <i class="fa fa-sort"></i>&nbsp; User
            </td>
            <td class="sortable" data-sort="total">
                <i class="fa fa-sort"></i>&nbsp; Total
            </td>
            <td class="sortable" data-sort="count">
                <i class="fa fa-sort"></i>&nbsp; Count
            </td>
            <td class="sortable" data-sort="created_at">
                <i class="fa fa-sort"></i>&nbsp; Created At
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{!! ($user = $item->user) && $user->exists ? '<a href="' . route('admin.users.edit', $user->id) . '" target="_blank">' . $user->full_name . '</a>' : 'Guest' !!}</td>
                    <td>{{ ($total = $item->grand_total) ? number_format($total, 2) . ' ' . config('shop.price.default_currency') : 'N/A' }}</td>
                    <td>{{ ($count = $item->count) ? $count : 'N/A' }}</td>
                    <td>{{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}</td>
                    <td>
                        {!! button()->viewRecord(route('admin.carts.view', $item->id), ['style' => 'margin-left: 0;']) !!}
                        {!! button()->deleteRecord(route('admin.carts.destroy', $item->id)) !!}
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