<header>
    @section('header')
        <button>
            <i class="fa fa-bars"></i>
        </button>

        @include('layouts::admin.partials._logout')
        @include('layouts::admin.partials._languages')

        {!! Breadcrumbs::render() !!}
    @show
</header>