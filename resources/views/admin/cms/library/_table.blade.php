<table cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="original_name">
            <i class="fa fa-sort"></i>&nbsp; Name
        </td>
        <td class="sortable" data-sort="size">
            <i class="fa fa-sort"></i>&nbsp; Size
        </td>
        <td class="actions-library">Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>
                    <img src="{{ $item->type_icon }}" title="{{ $types[$item->type] }}" class="library-aligned-image" />
                    <span class="library-aligned-text">{{ $item->original_name }}</span>
                </td>
                <td>
                    {{ $item->size . ' MB' }}
                </td>
                <td>
                    {!! button()->download('admin.library.download', ['id' => $item->id]) !!}
                    {!! button()->view('admin.library.show', ['id' => $item->id], ['target' => '_blank', 'title' => 'view cacat']) !!}
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