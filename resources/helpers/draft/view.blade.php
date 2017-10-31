@section('footer')
    <section class="left">
        {!! button()->action('Back To Original', session('draft_back_url_' . $draft->id), 'fa-chevron-left') !!}
    </section>
    <section class="right">
        @if(optional($draft->draftable::getDraftOptions())->draftLimit > 1)
            {!! button()->saveAsNew(route('admin.drafts.create', $draft->id)) !!}
        @endif
        @permission('drafts-save')
        {!! button()->saveElsewhere(route('admin.drafts.update', $draft->id)) !!}
        @endpermission
        @permission('drafts-publish')
        {!! button()->publishDraft(route('admin.drafts.publish', $draft->id)) !!}
        @else
            {!! button()->saveForApproval(route('admin.drafts.approval'), $approvalUrl) !!}
            @endpermission
    </section>
@endsection

@php
    DB::rollBack();

    if (@array_key_exists(\App\Traits\IsCacheable::class, class_uses($draft))) {
        $draft->clearQueryCache();
    }

    if (@array_key_exists(\App\Traits\IsCacheable::class, class_uses($model))) {
        $model->clearQueryCache();
    }
@endphp