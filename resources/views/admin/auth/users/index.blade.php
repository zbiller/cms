@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.auth.users._filter')
    </section>

    <section class="list">
        @include('admin.auth.users._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.users.create')) !!}
    </section>
@endsection