<section class="pagination">
    <a href="{{ $paginator->url(1) }}" class="first {!! $paginator->onFirstPage() ? 'inactive' : '' !!}">
        <i class="fa fa-angle-double-left"></i>
    </a>
    <a href="{{ $paginator->previousPageUrl()  }}" class="previous {!! $paginator->onFirstPage() ? 'inactive' : '' !!}">
        <i class="fa fa-angle-left"></i>
    </a>

    @php $currentPage = $paginator->currentPage(); @endphp
    @foreach($elements as $element)
        @if(is_array($element))
            @foreach ($element as $page => $url)
                @if($paginator->onFirstPage())
                    @if(($page == $currentPage) || ($page == $currentPage + 1) || ($page == $currentPage + 2))
                        <a href="{{ $url }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @elseif(!$paginator->hasMorePages())
                    @if(($page == $currentPage - 2) || ($page == $currentPage - 1) || ($page == $currentPage))
                        <a href="{{ $url }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @else
                    @if(($page == $currentPage - 1) || ($page == $currentPage) || ($page == $currentPage + 1))
                        <a href="{{ $url }}" class="{!! $page == $paginator->currentPage() ? 'current' : '' !!}">{{ $page }}</a>
                    @endif
                @endif
            @endforeach
        @endif
    @endforeach

    <a href="{{ $paginator->nextPageUrl() }}" class="next {!! $paginator->hasMorePages() ? '' : 'inactive' !!}">
        <i class="fa fa-angle-right"></i>
    </a>
    <a href="{{ $paginator->url($paginator->lastPage()) }}" class="last {!! $paginator->hasMorePages() ? '' : 'inactive' !!}">
        <i class="fa fa-angle-double-right"></i>
    </a>

    <div class="jump">
        {!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
        {!! form()->number('page', null, ['placeholder' => 'Page']) !!}
        {!! form()->submit('Go') !!}
        {!! form()->close() !!}
    </div>
</section>