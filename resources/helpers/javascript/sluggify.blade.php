@section('bottom_scripts')
    <script type="text/javascript">
        var from = $('input[name="' + '{{ $from }}' + '"]');
        var to = $('input[name="' + '{{ $to }}' + '"]');

        from.bind('keyup blur', function() {
            to.val(
                $(this).val().toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '')
            );
        });
    </script>
@append