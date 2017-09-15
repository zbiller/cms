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

        {!! form()->open(['url' => route('admin.activity.delete'), 'method' => 'DELETE', 'class' => 'left']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete All Activity', ['type' => 'submit', 'class' => 'btn green', 'onclick' => 'return confirm("Are you sure you want to delete all of the activity logs?")']) !!}
        {!! form()->close() !!}

        @if((int)config('activity.delete_records_older_than') > 0)
            {!! form()->open(['url' => route('admin.activity.clean'), 'method' => 'DELETE', 'class' => 'left']) !!}
            {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup Activity Older Than ' . (int)config('activity.delete_records_older_than') . ' Days', ['type' => 'submit', 'class' => 'btn blue no-margin-right', 'onclick' => 'return confirm("Are you sure you want to clean the activity log?")']) !!}
            {!! form()->close() !!}
        @endif
    </section>
@endsection