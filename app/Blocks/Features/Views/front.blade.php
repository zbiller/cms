@if($block->anchor)
	<a id="{{ $block->anchor }}"></a>
@endif

@if(isset($data->items) && count($data->items) > 0)
	<div class="section-seperator">
		<div class="content-md container">
			<div class="row">
				@foreach($data->items as $item)
					<div class="col-sm-4 sm-margin-b-50">
						<div class="wow fadeInLeft" data-wow-duration=".3" data-wow-delay=".3s">
							<h3>{{ $item->title }}</h3>
							<p>{!! nl2br($item->content) !!}</p>
							<a class="link" href="{{ $item->button_url }}">{{ $item->button_text }}</a>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif