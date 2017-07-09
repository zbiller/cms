@if ($breadcrumbs)
    @foreach ($breadcrumbs as $breadcrumb)
        @if (!$breadcrumb->last)
            <a href="{{ $breadcrumb->url }}" class="breadcrumb">
                {{ $breadcrumb->title }} <i class="fa fa-angle-double-right"></i>
            </a>
        @else
            <a class="breadcrumb last">{{ $breadcrumb->title }}</a>
        @endif
    @endforeach
@endif