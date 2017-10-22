<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

{!! Meta::tag('title') !!}
{!! Html::style(mix('css/admin/libs.css')) !!}
{!! Html::style(mix('css/admin/app.css')) !!}

@section('top_styles') @show
@section('top_scripts') @show