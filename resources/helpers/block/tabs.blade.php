@if($locations && is_array($locations) && !empty($locations))
    @foreach($locations as $location)
        <a href="#tab-{{ $location }}-blocks">{{ title_case(str_replace(['-', '_'], ' ', $location)) }} Blocks</a>
    @endforeach
@endif