@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.backup.backups._filter')
    </section>

    <section class="content">
        {!! form()->open(['url' => route('admin.backups.create'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-plus"></i>&nbsp; Create New Backup Checkpoint', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to create a new backup point?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.backup.backups._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
    </section>
@endsection