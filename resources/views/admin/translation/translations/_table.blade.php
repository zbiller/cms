<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <td class="sortable" data-sort="locale">
                <i class="fa fa-sort"></i>&nbsp; Language
            </td>
            <td class="sortable" data-sort="group">
                <i class="fa fa-sort"></i>&nbsp; Group
            </td>
            <td class="sortable visible-on-mobile-and-up" data-sort="key">
                <i class="fa fa-sort"></i>&nbsp; Key
            </td>
            <td class="sortable visible-on-tablet-and-up" data-sort="value">
                <i class="fa fa-sort"></i>&nbsp; Value
            </td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
    @if($items->count() > 0)
        @foreach($items as $index => $item)
            <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                <td>{{ $item->locale_formatted ?: 'N/A' }}</td>
                <td>{{ $item->group_formatted ?: 'N/A' }}</td>
                <td class="visible-on-mobile-and-up">{{ $item->key_formatted ?: 'N/A' }}</td>
                <td class="visible-on-tablet-and-up">{{ $item->value ? str_limit(strip_tags($item->value), 30) : 'N/A' }}</td>
                <td>
                    {!! button()->editRecord(route('admin.translations.edit', $item->id)) !!}
                    {!! button()->deleteRecord(route('admin.translations.destroy', $item->id)) !!}
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