@if($block->anchor)
	<a id="{{ $block->anchor }}"></a>
@endif

<div>
	<img src="{{ uploaded($data->header_wallpaper)->url('wallpaper_default') }}" />
</div>