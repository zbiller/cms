@component('mail::message')

<h1>Hello</h1>

<p>The sitemap files for <a href="{{ config('app.url') }}">{{ config('app.url') }}</a> have failed to generate!</p>
<p>You can re-run the command from <a href="{{ route('admin.sitemap.index') }}">here</a>.</p>

@if($message)
@component('mail::panel')

<p>Sitemap Generation Error:<br /><strong>{{ $message }}</strong></p>

@endcomponent
@endif

<hr />
<p>Thank you!</p>

@endcomponent