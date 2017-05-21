@if($block->anchor)
	<a id="{{ $block->anchor }}"></a>
@endif

<div class="content-md container">
	<div class="row margin-b-40">
		<div class="col-sm-6">
			<h2>{{ $data->title }}</h2>
			<p>{!! nl2br($data->description) !!}</p>
		</div>
	</div>
	@if(isset($data->items) && count($data->items) > 0)
		<div class="swiper-slider swiper-clients">
			<div class="swiper-wrapper">
				@foreach($data->items as $item)
					<div class="swiper-slide">
						<img class="swiper-clients-img" src="{{ uploaded($item->client_image)->url('default') }}" alt="{{ $item->caption }}">
					</div>
				@endforeach
			</div>
		</div>
	@endif
</div>