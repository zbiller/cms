<table class="table"
    cellspacing="0" cellpadding="0" border="0"
    data-orderable="{{ empty(request()->all()) ? 'true' : 'false' }}"
    data-order-url="{{ route('admin.attributes.order', $set) }}"
    data-order-model="{{ \App\Models\Shop\Attribute::class }}"
    data-order-token="{{ csrf_token() }}"
>
    <thead>
        <tr class="nodrag nodrop">
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Name
            </td>
            <td class="sortable" data-sort="slug">
                <i class="fa fa-sort"></i>&nbsp; Slug
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr id="{{ $item->id }}" class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->name ?: 'N/A' }}</td>
                    <td>{{ $item->slug ?: 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.attributes.edit', ['set' => $set->id, 'id' => $item->id])) !!}
                        {!! button()->deleteRecord(route('admin.attributes.destroy', ['set' => $set->id, 'id' => $item->id])) !!}
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