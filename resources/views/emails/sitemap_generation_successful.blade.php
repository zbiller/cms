@component('mail::message')

<h1>Hello</h1>

<p>The sitemap files for <a href="{{ config('app.url') }}">{{ config('app.url') }}</a> have been generated successfully!</p>
<p>You can check the condition of the sitemap <a href="{{ route('admin.sitemap.index') }}">here</a>.</p>

<hr />
<p>Thank you!</p>

@endcomponent