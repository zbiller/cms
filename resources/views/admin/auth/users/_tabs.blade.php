<a href="#tab-1">Basic</a>
<a href="#tab-2">Personal</a>

@if($item->exists)
    <a href="{{ route('admin.addresses.index', $item->id) }}" class="real-tab">Addresses</a>
@endif