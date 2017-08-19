@extends('layouts::admin.default')

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
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.users.create')) !!}
    </section>
@endsection