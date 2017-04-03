<footer>
    @yield('footer')
</footer>

{{ Html::script('/build/admin/js/super.min.js') }}
{{ Html::script('/build/plugins/tinymce/js/tinymce/tinymce.min.js', ['async']) }}

@section('bottom_styles') @show
@section('bottom_scripts') @show