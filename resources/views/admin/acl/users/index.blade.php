@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Users</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.acl.users._filter')
    </section>

    <section class="list">
        @include('admin.acl.users._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}
        {!! button()->addRecord(route('admin.users.create')) !!}
    </section>
@endsection