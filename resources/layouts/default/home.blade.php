<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    @include('layouts::default.partials._head')
</head>
<body>
@include('layouts::default.partials._header')

<main>
    @yield('content')
</main>

@include('layouts::default.partials._footer')
</body>
</html>