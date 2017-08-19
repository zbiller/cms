@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.cms.uploads._filter')
    </section>

    <section class="upload">
        @include('admin.cms.uploads._upload')
    </section>

    <section class="list">
        @include('admin.cms.uploads._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
    </section>
@endsection