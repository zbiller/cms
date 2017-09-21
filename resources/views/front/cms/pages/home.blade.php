@extends('layouts::' . $page->layout->blade)

@section('content')
    <h1>Home Page</h1>

    @for($i=1; $i<=3; $i++)
        <a href="cacat-url/{{ $i }}">Cacat {{ $i }}</a><br />
    @endfor
@endsection