@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.acl.admins._filter')
    </section>

    <section class="list">
        @include('admin.acl.admins._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.admins.create')) !!}
    </section>
@endsection