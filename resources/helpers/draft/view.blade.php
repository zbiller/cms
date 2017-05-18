@section('footer')
    <section class="actions left">
        {!! button()->action('Back To Original', session('draft_back_url_' . $draft->id), 'fa-chevron-left') !!}
    </section>
    <section class="actions">
        {!! button()->saveNewDraft('admin.drafts.create', ['id' => $draft->id]) !!}
        {!! button()->saveDraft('admin.drafts.update', ['id' => $draft->id]) !!}
        {!! button()->publishDraft('admin.drafts.publish', ['id' => $draft->id]) !!}
    </section>
@endsection
@php DB::rollBack(); @endphp