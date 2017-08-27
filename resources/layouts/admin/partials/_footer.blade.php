<footer>
    @yield('footer')
</footer>

{!! Html::script(mix('js/admin/libs.js')) !!}
{!! Html::script(mix('js/admin/app.js')) !!}
{!! Html::script('/js/scripts/tinymce/tinymce.min.js', ['async']) !!}

@section('bottom_styles') @show
@section('bottom_scripts') @show