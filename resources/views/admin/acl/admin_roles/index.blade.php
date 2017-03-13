@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Admin Roles</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.acl.admin_roles._filter')
    </section>

    <section class="list">
        @include('admin.acl.admin_roles._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination()->render($items, 'admin') !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.admin_roles.create') !!}
    </section>
@endsection