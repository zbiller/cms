@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Admin Users</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.acl.admin_users._filter')
    </section>

    <section class="list">
        @include('admin.acl.admin_users._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination()->render($items, 'admin') !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.admin_users.create') !!}
    </section>
@endsection