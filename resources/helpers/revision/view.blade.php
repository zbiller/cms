@section('footer')
    <section class="actions left">
        {!! button()->action('Back To Original', session('revision_back_url_' . $revision->id), 'fa-chevron-left') !!}
    </section>
    <section class="actions">
        {!! button()->rollback(route('admin.revisions.rollback', $revision->id)) !!}
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

@php DB::rollBack(); @endphp