<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

{!! Meta::tags('title', 'image', 'description', 'keywords') !!}
{!! Html::style(elixir('assets/css/front/app.css')) !!}
{!! setting()->value('analytics-code') !!}

@if($page && $page->exists)
    {!! page()->canonical($page) !!}
@endif

@section('top_styles') @show
@section('top_scripts') @show