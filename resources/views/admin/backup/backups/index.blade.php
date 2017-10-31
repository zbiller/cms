@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.backup.backups._filter')
    </section>

    <section class="content content-half one">
        {!! form()->open(['url' => route('admin.backups.create'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-plus"></i>&nbsp; Create new backup checkpoint', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to create a new backup point?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-half two">
        {!! form()->open(['url' => route('admin.backups.clear'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Remove all backups', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all of the backups?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.backup.backups._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
    </section>
@endsection