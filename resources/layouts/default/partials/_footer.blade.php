{{ Html::script(elixir('assets/js/front/app.js')) }}

@section('bottom_styles') @show
@section('bottom_scripts') @show

@php(preview()->handle())