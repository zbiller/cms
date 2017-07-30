<a href="#tab-1">Primary Information</a>

@if($item->exists)
    <a href="{{ route('admin.attributes.index', $item->id) }}" class="real-tab">Attributes</a>
@endif