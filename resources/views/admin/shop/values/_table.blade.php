<table
    cellspacing="0" cellpadding="0" border="0"
    data-orderable="{{ empty(request()->all()) ? 'true' : 'false' }}"
    data-order-url="{{ route('admin.values.order', ['set' => $set, 'attribute' => $attribute]) }}"
    data-order-model="{{ \App\Models\Shop\Value::class }}"
    data-order-token="{{ csrf_token() }}"
>
    <thead>
        <tr class="nodrag nodrop">
            <td class="sortable" data-sort="name">
                <i class="fa fa-sort"></i>&nbsp; Value
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @if($items->count() > 0)
            @foreach($items as $index => $item)
                <tr id="{{ $item->id }}" class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                    <td>{{ $item->value ?: 'N/A' }}</td>
                    <td>
                        {!! button()->editRecord(route('admin.values.edit', ['set' => $set, 'attribute' => $attribute, 'id' => $item->id])) !!}
                        {!! button()->deleteRecord(route('admin.values.destroy', ['set' => $set, 'attribute' => $attribute, 'id' => $item->id])) !!}
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