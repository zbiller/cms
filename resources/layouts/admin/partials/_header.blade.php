<header>
    @section('header')
        <button>
            <i class="fa fa-bars"></i>
        </button>

        @include('layouts::admin.partials._logout')
        @include('layouts::admin.partials._languages')
        @include('layouts::admin.partials._notifications')

        {!! Breadcrumbs::render() !!}
    @show
</header>