@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.auth.activity._filter')
    </section>

    <section class="list">
        @include('admin.auth.activity._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}

        @if((int)config('activity.delete_records_older_than') > 0)
            {!! form()->open(['url' => route('admin.activity.clean'), 'method' => 'DELETE', 'class' => 'left']) !!}
            {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Remove Activity Older Than ' . (int)config('activity.delete_records_older_than') . ' Days', ['type' => 'submit', 'class' => 'btn blue', 'onclick' => 'return confirm("Are you sure you want to clean the activity log?")']) !!}
            {!! form()->close() !!}
        @endif

        {!! form()->open(['url' => route('admin.activity.delete'), 'method' => 'DELETE', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete All Activity Logs', ['type' => 'submit', 'class' => 'btn red no-margin-right', 'onclick' => 'return confirm("Are you sure you want to delete all of the activity logs?")']) !!}
        {!! form()->close() !!}
    </section>
@endsection