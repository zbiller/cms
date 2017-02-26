<ul class="main-menu">
    @foreach($menu->roots() as $item)
        <li>
            <a{!! $item->url() ? ' href="' . $item->url() . '"' : '' !!}{!! $item->active() ? ' class="active"' : '' !!}>
                <i class="fa {!! $item->data('icon') !!} fa-fw fa-lg"></i>&nbsp; {{ $item->name() }}
            </a>
            @if($menu->children($item)->count() > 0)
                <ul class="sub-menu{!! $item->active() ? ' active' : '' !!}">
                    @foreach($menu->children($item) as $child)
                        <li>
                            <a href="{!! $child->url() ?: '#' !!}" class="{!! $child->active() ? 'active' : '' !!}">
                                {{ $child->name() }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>