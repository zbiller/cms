@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Uploads</h1>
@endsection

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
        {!! button()->update() !!}
        {!! form()->open(['url' => route('admin.uploads.delete'), 'method' => 'DELETE', 'class' => 'right']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove unused uploads', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure? All unused uploads will be permanently deleted.")']) !!}
        {!! form()->close() !!}
    </section>
@endsection