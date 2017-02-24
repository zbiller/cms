<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    @include('layouts::admin.partials._head')
</head>
<body>
    @include('layouts::admin.partials._flash')
    @include('layouts::admin.partials._header')
    @include('layouts::admin.partials._sidebar')

    <main>
        @yield('content')
    </main>

    @include('layouts::admin.partials._footer')
</body>
</html>