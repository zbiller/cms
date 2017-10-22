@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.auth.notifications._filter')
    </section>

    <section class="content content-third one">
        {!! form()->open(['url' => route('admin.notifications.mark_all_as_read'), 'method' => 'POST']) !!}
        {!! form()->button('<i class="fa fa-check-circle"></i>&nbsp; Mark all notifications as read', ['type' => 'submit', 'class' => 'btn blue full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to mark all your notifications as being read?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third two">
        {!! form()->open(['url' => route('admin.notifications.clean'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-ban"></i>&nbsp; Cleanup already read notifications', ['type' => 'submit', 'class' => 'btn green full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to remove all your read notifications?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="content content-third three">
        {!! form()->open(['url' => route('admin.notifications.delete'), 'method' => 'DELETE']) !!}
        {!! form()->button('<i class="fa fa-trash"></i>&nbsp; Delete all notifications', ['type' => 'submit', 'class' => 'btn red full centered no-margin visible-text', 'onclick' => 'return confirm("Are you sure you want to delete absolutely all of your notifications?")']) !!}
        {!! form()->close() !!}
    </section>

    <section class="list">
        @include('admin.auth.notifications._table', ['items' => $items])
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->updateAction() !!}
    </section>
@endsection