<footer>
    @yield('footer')
</footer>

{{ Html::script(elixir('assets/js/admin/app.js')) }}
{{ Html::script('/build/assets/plugins/tinymce/js/tinymce/tinymce.min.js', ['async']) }}

@section('bottom_styles') @show
@section('bottom_scripts') @show