<header>
    @section('header')
        <button>
            <i class="fa fa-bars"></i>
        </button>

        {!! form()->open(['url' => route('admin.logout'), 'method' => 'post']) !!}
        {!! form()->button('<i class="fa fa-power-off"></i>&nbsp; Logout', ['type' => 'submit', 'class' => 'logout btn right']) !!}
        {!! form()->close() !!}

        {!! Breadcrumbs::render() !!}
    @show
</header>