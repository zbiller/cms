<footer>
    @yield('footer')
</footer>

{{ Html::script('//cloud.tinymce.com/stable/tinymce.min.js') }}
{{ Html::script('/build/admin/js/super.min.js') }}

@section('bottom_styles') @show
@section('bottom_scripts') @show