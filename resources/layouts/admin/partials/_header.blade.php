<header>
    @section('header')
        <button>
            <i class="fa fa-bars"></i>
        </button>
        {!! Form::open(['url' => route('admin.logout'), 'method' => 'post']) !!}
        {!! Form::button('<i class="fa fa-power-off"></i>&nbsp; Logout', ['type' => 'submit', 'class' => 'btn right']) !!}
        {!! Form::close() !!}
    @show
</header>