@php($canPublish = auth()->user()->isDeveloper() || auth()->user()->hasPermission('drafts-publish'))
@php($canSubmitApproval = auth()->user()->hasPermission('drafts-approval'))
<thead>
<tr class="even">
    <td class="desktop-only">#</td>
    <td>Created By</td>
    <td>Created At</td>
    <td class="{{ $canPublish ? 'actions-drafts' : ($canSubmitApproval ? 'actions-approval' : 'actions-default') }}">Actions</td>
</tr>
</thead>
<tbody>
@if($drafts->count())
    @foreach($drafts as $index => $draft)
        <tr>
            <td class="desktop-only">{{ $index + 1 }}</td>
            <td>{{ $draft->user ? $draft->user->full_name : 'N/A' }}</td>
            <td>{{ $draft->created_at ?: 'N/A' }}</td>
            <td>
                @if($canPublish)
                    <a href="{{ route('admin.drafts.publish', $draft->id) }}" class="draft-publish btn green no-margin-left no-margin-top no-margin-bottom double-margin-right">
                        <i class="fa fa-check-square-o"></i>&nbsp; Publish
                    </a>
                @elseif($canSubmitApproval)
                    <a href="{{ route('admin.drafts.approval') }}" data-approval-url="{{ route($route, $parameters + ['draft' => $draft->id]) }}" class="draft-approval btn green no-margin-left no-margin-top no-margin-bottom double-margin-right">
                        <i class="fa fa-thumbs-up"></i>&nbsp; Submit For Approval
                    </a>
                @endif
                <a href="{{ route($route, $parameters + ['draft' => $draft->id]) }}" class="btn yellow no-margin-top no-margin-bottom no-margin-left {!! !(auth()->user()->isDeveloper() || auth()->user()->hasPermission('drafts-view')) ? 'disabled' : '' !!}">
                    <i class="fa fa-eye"></i>&nbsp; View
                </a>
                <a href="{{ route('admin.drafts.remove', $draft->id) }}" class="draft-delete btn red no-margin-top no-margin-bottom no-margin-right {!! !(auth()->user()->isDeveloper() || auth()->user()->hasPermission('drafts-delete')) ? 'disabled' : '' !!}">
                    <i class="fa fa-times"></i>&nbsp; Remove
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr class="no-drafts">
        <td colspan="10">
            There are no drafts for this record
        </td>
    </tr>
@endif
</tbody>