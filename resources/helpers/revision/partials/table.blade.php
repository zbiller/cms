<thead>
    <tr class="even">
        <td class="desktop-only">#</td>
        <td>Modified By</td>
        <td>Modified At</td>
        <td class="actions-revisions">Actions</td>
    </tr>
</thead>
<tbody>
    @if($revisions->count())
        @foreach($revisions as $index => $revision)
            <tr>
                <td class="desktop-only">{{ $index + 1 }}</td>
                <td>{{ $revision->user ? $revision->user->full_name : 'N/A' }}</td>
                <td>{{ $revision->created_at ?: 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.revisions.rollback', $revision->id) }}" class="revision-rollback btn green no-margin-left no-margin-top no-margin-bottom" onclick="return confirm('Are you sure you want to rollback the record to this revision?')">
                        <i class="fa fa-undo"></i>&nbsp; Rollback
                    </a>
                    <a href="{{ route($route, $revision->id) }}" class="btn yellow no-margin-top no-margin-bottom">
                        <i class="fa fa-eye"></i>&nbsp; View
                    </a>
                    <a href="{{ route('admin.revisions.destroy', $revision->id) }}" class="revision-delete btn red no-margin-top no-margin-bottom no-margin-right">
                        <i class="fa fa-times"></i>&nbsp; Remove
                    </a>
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-blocks-assigned">
            <td colspan="10">
                There are no revisions for this record
            </td>
        </tr>
    @endif
</tbody>