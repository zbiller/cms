@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Activity Logs</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.acl.activity_logs._filter')
    </section>

    <section class="list">
        @include('admin.acl.activity_logs._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updatePage() !!}

        @if((int)config('activity-log.delete_records_older_than') > 0)
            {!! form()->open(['url' => route('admin.activity_logs.clean'), 'method' => 'DELETE', 'class' => 'left']) !!}
            {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup Activity Older Than ' . (int)config('activity-log.delete_records_older_than') . ' Days', ['type' => 'submit', 'class' => 'btn green', 'onclick' => 'return confirm("Are you sure you want to clean the activity log?")']) !!}
            {!! form()->close() !!}
        @endif

        {!! form()->open(['url' => route('admin.activity_logs.delete'), 'method' => 'DELETE', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete All Activity', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure you want to delete all of the activity logs?")']) !!}
        {!! form()->close() !!}
    </section>
@endsection