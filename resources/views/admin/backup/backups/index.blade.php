@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.backup.backups._filter')
    </section>

    <section class="list">
        @include('admin.backup.backups._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
        {!! form()->open(['url' => route('admin.backups.create'), 'method' => 'POST', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-plus"></i>&nbsp; Create New', ['type' => 'submit', 'class' => 'btn blue', 'onclick' => 'return confirm("Are you sure you want to create a new backup point?")']) !!}
        {!! form()->close() !!}
    </section>
@endsection