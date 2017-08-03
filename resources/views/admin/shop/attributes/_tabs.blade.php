<a href="#tab-1">Basic</a>

@if($item->exists)
    <a href="{{ route('admin.values.index', ['set' => $set, 'attribute' => $item->id]) }}" class="real-tab">Values</a>
@endif