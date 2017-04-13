@extends('layouts::default.' . $page->layout->blade)

@section('content')
    <h1>{{ $page->metadata->title }}</h1>
    <h2>{{ $page->metadata->subtitle }}</h2>
    {!! $page->metadata->content !!}
@endsection