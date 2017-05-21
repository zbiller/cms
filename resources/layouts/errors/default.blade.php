<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,500,700" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            width: 100%;
            display: table;
            font-family: 'Lato';
        }

        main {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        h1 {
            color: #B0BEC5;
            font-size: 10em;
            margin: 0;
            font-weight: 700;
        }

        h2 {
            color: #B0BEC5;
            font-size: 2em;
            margin: 0 0 50px 0;
            font-weight: 300;
        }

        p {
            max-width: 50%;
            color: #99ACB5;
            font-size: 1.05em;
            margin: 0 auto 50px auto;
            font-weight: 300;
            line-height: 24px;
        }

        a {
            color: #4AAEE3;
            font-size: 1.05em;
            margin: 0;
            font-weight: 300;
            text-decoration: none;
        }

        a:hover {
            color: #45576D;
        }
    </style>
</head>
<body>
    <main>
        @section('content')
            <a href="{{ url('/') }}">return to homepage</a>
        @show
    </main>
</body>
</html>