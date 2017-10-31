@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="#tab-1">Basic</a>
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => url()->current(), 'method' => 'POST', 'class' => 'form']) !!}
        {!! validation('admin')->errors() !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->textarea('analytics-code', 'Analytics Code', setting()->value('analytics-code'), ['style' => 'height: 200px;']) !!}
        </div>
        {!! form_admin()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="left">
        {!! button()->cancelAction(route('admin')) !!}
    </section>
    <section class="right">
        {!! button()->saveRecord() !!}
    </section>
@endsection

