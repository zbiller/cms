<header>
    @section('header')
        <button>
            <i class="fa fa-bars"></i>
        </button>

        {!! form()->open(['url' => route('admin.logout'), 'method' => 'post']) !!}
        {!! form()->button('<i class="fa fa-power-off"></i>&nbsp; Logout', ['type' => 'submit', 'class' => 'logout btn right']) !!}
        {!! form()->close() !!}

        <div class="dropdown">
            <a href="#" class="dropdown-current">
                English &nbsp;<i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-choices">
                <li>
                    <a href="#">English</a>
                    <a href="#">Romanian</a>
                    <a href="#">French</a>
                    <a href="#">German</a>
                </li>
            </ul>
        </div>

        {!! Breadcrumbs::render() !!}
    @show
</header>