@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.auth.roles._filter')
    </section>

    <section class="list">
        @include('admin.auth.roles._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.roles.create')) !!}
    </section>
@endsection