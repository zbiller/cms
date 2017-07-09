@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.layouts._tabs')
    </section>

    <section class="view">
        {!! form_admin()->open(['url' => route('admin.layouts.store'), 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.layouts._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.layouts.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection