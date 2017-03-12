<footer>
    @yield('footer')
</footer>

{{ Html::script('//cloud.tinymce.com/stable/tinymce.min.js?apiKey=lfc3z2mhix7d8cjtwsfwz179fd41jri6qev78dn67d1kb6g5') }}
{{ Html::script('/build/admin/js/super.min.js') }}
{{ Html::script('vendor/jsvalidation/js/jsvalidation.js') }}

@section('bottom_styles') @show
@section('bottom_scripts') @show