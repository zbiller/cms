@section('footer')
    <section class="actions left">
        {!! button()->action('Back To Original', session('draft_back_url_' . $draft->id), 'fa-chevron-left') !!}
    </section>
    <section class="actions">
        {!! button()->saveAsNew(route('admin.drafts.create', $draft->id)) !!}
        {!! button()->saveElsewhere(route('admin.drafts.update', $draft->id)) !!}
        {!! button()->publishDraft(route('admin.drafts.publish', $draft->id)) !!}
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