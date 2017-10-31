@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        <a href="#tab-1">Basic</a>
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => url()->current(), 'method' => 'POST', 'class' => 'form']) !!}
        {!! validation('admin')->errors() !!}

        <div id="tab-1" class="tab">
            {!! form_admin()->text('company-name', 'Company Name', setting()->value('company-name')) !!}
            {!! form_admin()->text('company-email', 'Company Email', setting()->value('company-email')) !!}
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

