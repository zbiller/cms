<a href="#tab-1">Basic</a>
<a href="#tab-2">Filtering</a>

@if($item->exists)
    <a href="{{ route('admin.attribute_values.index', ['set' => $set, 'attribute' => $item->id]) }}" class="real-tab">Values</a>
@endif