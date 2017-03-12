@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Admin Groups</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.admin.groups._filter')
    </section>

    <section class="list">
        @include('admin.admin.groups._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination()->render($items, 'admin') !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.admin.groups.create') !!}
    </section>
@endsection