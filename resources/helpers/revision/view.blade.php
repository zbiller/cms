@section('footer')
    <section class="actions left">
        {!! button()->action('Back To Original', session('revision_back_url_' . $revision->id), 'fa-chevron-left') !!}
    </section>
    <section class="actions">
        @permission('revisions-rollback')
            {!! button()->rollbackRevision(route('admin.revisions.rollback', $revision->id)) !!}
        @endpermission
    </section>
@endsection

@section('bottom_scripts')
    <script type="text/javascript">
        $(function () {
            disable.form();
        });
    </script>
@append

@php
    DB::rollBack();

    if (@array_key_exists(\App\Traits\IsCacheable::class, class_uses($revision))) {
        $revision->clearQueryCache();
    }

    if (@array_key_exists(\App\Traits\IsCacheable::class, class_uses($model))) {
        $model->clearQueryCache();
    }
@endphp