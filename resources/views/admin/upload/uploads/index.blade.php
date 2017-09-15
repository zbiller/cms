@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.upload.uploads._filter')
    </section>

    <section class="upload">
        @include('admin.upload.uploads._upload')
    </section>

    <section class="list">
        @include('admin.upload.uploads._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
    </section>
@endsection