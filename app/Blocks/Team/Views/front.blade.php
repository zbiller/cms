@if($block->anchor)
	<a id="{{ $block->anchor }}"></a>
@endif

<div class="bg-color-sky-light">
	<div class="content-md container">
		<div class="row margin-b-40">
			<div class="col-sm-6">
				<h2>{{ $data->title }}</h2>
				<p>{!! nl2br($data->description) !!}</p>
			</div>
		</div>

		@if(isset($data->items) && count($data->items))
		<div class="row">
			@foreach($data->items as $item)
			<div class="col-sm-4 sm-margin-b-50">
				<div class="bg-color-white margin-b-20">
					<div class="wow zoomIn" data-wow-duration=".3" data-wow-delay=".1s">
						<img class="img-responsive" src="{{ uploaded($item->team_image)->url('desktop') }}" alt="{{ $item->caption }}">
					</div>
				</div>
				<h4>
					<a href="{{ $item->button_url }}">{{ $item->name }}</a>
					<span class="text-uppercase margin-l-20">{{ $item->position }}</span>
				</h4>
				<p>{!! nl2br($item->biography) !!}</p>
				<a class="link" href="{{ $item->button_url }}">
					{{ $item->button_text }}
				</a>
			</div>
			@endforeach
		</div>
		@endif
	</div>
</div>