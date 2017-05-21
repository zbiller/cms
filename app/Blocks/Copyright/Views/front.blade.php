@if($block->anchor)
	<a id="{{ $block->anchor }}"></a>
@endif

<div class="content container">
	<div class="row">
		<div class="col-xs-6">
			<img class="footer-logo" src="{{ uploaded($data->footer_logo)->url('logo_default') }}" alt="Acidus Logo">
		</div>
		<div class="col-xs-6 text-right">
			<p class="margin-b-0">
				{!! nl2br($data->copyright) !!}
			</p>
		</div>
	</div>
</div>