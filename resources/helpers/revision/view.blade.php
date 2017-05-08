@section('footer')
    <section class="actions left">
        {!! button()->action('Back To Original', url()->previous(), 'fa-chevron-left') !!}
    </section>
    <section class="actions">
        {!! button()->rollback('admin.revisions.rollback', ['id' => $revision->id]) !!}
    </section>
@endsection

@section('bottom_scripts')
    <script type="text/javascript">
        $(function () {
            setTimeout(function () {
                tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            }, 1500);
        });
    </script>
@append

@php
    DB::rollBack();
    session()->keep([
        'revision_rollback_url'
    ]);
@endphp