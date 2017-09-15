@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="real-tab">Back To User</a>
    </section>

    <section class="filters">
        @include('admin.auth.addresses._filter')
    </section>

    <section class="list">
        @include('admin.auth.addresses._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! button()->addRecord(route('admin.addresses.create', $user->id)) !!}
    </section>
@endsection