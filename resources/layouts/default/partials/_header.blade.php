<!--========== HEADER ==========-->
<header class="header">
    <!-- Navbar -->
    <nav class="navbar" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="menu-container">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="toggle-icon"></span>
                </button>

                <!-- Logo -->
                <div class="navbar-logo">
                    <a class="navbar-logo-wrap" href="#">
                        <img class="navbar-logo-img" src="{{ url('/build/assets/img/front/logo.png') }}" alt="Acidus Logo">
                    </a>
                </div>
                <!-- End Logo -->
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse nav-collapse">
                <div class="menu-container">
                    <ul class="navbar-nav navbar-nav-right">
                        @foreach(menu()->get('top') as $index => $menu)
                            <!-- Menu Item -->
                            <li class="nav-item">
                                <a class="nav-item-child {{ $index == 0 ? 'active' : '' }}" href="{{ $menu->url }}" {{ $menu->metadata->new_window == 1 ? 'target="_blank"' : '' }}>
                                    {{ $menu->name }}
                                </a>
                            </li>
                            <!-- End Menu Item -->
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- End Navbar Collapse -->
        </div>
    </nav>
    <!-- Navbar -->
</header>
<!--========== END HEADER ==========-->