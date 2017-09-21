@component('mail::message')

<h1>Hello</h1>

<p>The sitemap files for <a href="{{ config('app.url') }}">{{ config('app.url') }}</a> have {{ $status === true ? 'been generated successfully' : 'failed to generate' }}!</p>
<p>{{ $status === true ? 'You can check the condition of the sitemap' : 'You can re-run the command from' }} <a href="{{ config('app.url') }}/admin/sitemap">here</a>.</p>

<hr />
<p>Thank you!</p>

@endcomponent