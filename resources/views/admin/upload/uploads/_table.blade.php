<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <td class="sortable" data-sort="original_name">
            <i class="fa fa-sort"></i>&nbsp; Name
        </td>
        <td class="sortable" data-sort="size">
            <i class="fa fa-sort"></i>&nbsp; Size
        </td>
        <td class="actions-upload">Actions</td>
    </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>
                    <img src="{{ $item->type_icon }}" title="{{ $types[$item->type] }}" class="upload-aligned-image" width="30" height="30" />
                    <span class="upload-aligned-text">{{ $item->original_name ?: 'N/A' }}</span>
                </td>
                <td>
                    {{ $item->size_in_mb . ' MB' }}
                </td>
                <td>
                    {!! button()->downloadFile(route('admin.uploads.download', $item->id)) !!}
                    {!! button()->viewRecord(route('admin.uploads.show', $item->id), ['target' => '_blank']) !!}
                    {!! button()->deleteRecord(route('admin.uploads.destroy', $item->id)) !!}
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