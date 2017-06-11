<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<link href="http://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet" type="text/css">

{!! Meta::tags('title', 'image', 'description', 'keywords') !!}

{{ Html::style(elixir('assets/css/front/app.css')) }}

{!! setting()->value('analytics-code') !!}

@section('top_styles') @show
@section('top_scripts') @show