@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.auth.activity._filter')
    </section>

    <section class="content content-half one">
        {!! form()->open(['url' => route('admin.activity.clean'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup activity older than ' . (int)config('activity.delete_records_older_than') . ' days', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all of the activity logs older than the given time limit?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-half two">
        {!! form()->open(['url' => route('admin.activity.delete'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete all activity logs', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to delete all of the activity logs?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.auth.activity._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="right">
        {!! button()->updateAction() !!}
    </section>
@endsection