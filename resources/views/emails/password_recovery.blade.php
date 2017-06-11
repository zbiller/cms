@component('mail::message')

@if(isset($top))
{!! nl2br($top) !!}
@endif

@if($button)
@component('mail::button', ['url' => isset($url) ? $url : url('/')])
{!! $button !!}
@endcomponent
@endif

@if(isset($bottom))
{!! nl2br($bottom) !!}
@endif

@component('mail::subcopy')
Thank you!<br>
{{ setting()->value('company-name') ?: config('app.name') }}
@endcomponent
@endcomponent