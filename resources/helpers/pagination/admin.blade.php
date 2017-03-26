<section class="pagination">
    <a href="{{ $paginator->appends(request()->input())->url(1) }}" class="first {!! $paginator->onFirstPage() ? 'inactive' : '' !!}">
        <i class="fa fa-angle-double-left"></i>
    </a>
    <a href="{{ $paginator->appends(request()->input())->previousPageUrl()  }}" class="previous {!! $paginator->onFirstPage() ? 'inactive' : '' !!}">
        <i class="fa fa-angle-left"></i>
    </a>

    @php $currentPage = $paginator->currentPage(); @endphp

    @foreach($elements as $element)
        @if(is_array($element))
            @foreach ($element as $page => $url)
                @if($paginator->onFirstPage())
                    @if(($page == $currentPage) || ($page == $currentPage + 1) || ($page == $currentPage + 2))
                        <a href="{{ $paginator->appends(request()->input())->url($page) }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @elseif(!$paginator->hasMorePages())
                    @if(($page == $currentPage - 2) || ($page == $currentPage - 1) || ($page == $currentPage))
                        <a href="{{ $paginator->appends(request()->input())->url($page) }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @else
                    @if(($page == $currentPage - 1) || ($page == $currentPage) || ($page == $currentPage + 1))
                        <a href="{{ $paginator->appends(request()->input())->url($page) }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @endif
            @endforeach
        @endif
    @endforeach

    <a href="{{ $paginator->appends(request()->input())->nextPageUrl() }}" class="next {!! $paginator->hasMorePages() ? '' : 'inactive' !!}">
        <i class="fa fa-angle-right"></i>
    </a>
    <a href="{{ $paginator->appends(request()->input())->url($paginator->lastPage()) }}" class="last {!! $paginator->hasMorePages() ? '' : 'inactive' !!}">
        <i class="fa fa-angle-double-right"></i>
    </a>

    <div class="jump">
        {!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
            @foreach(request()->input() as $key => $value)
                @if(is_array($value))
                    @foreach($value as $index => $val)
                        {!! form()->hidden("{$key}[{$index}]", $val) !!}
                    @endforeach
                @else
                    {!! form()->hidden($key, $value) !!}
                @endif
            @endforeach
            {!! form()->number('page', null, ['placeholder' => 'Page']) !!}
            {!! form()->submit('Go') !!}
        {!! form()->close() !!}
    </div>
</section>