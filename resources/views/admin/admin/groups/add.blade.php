@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Add Admin Group</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.admin.groups._tabs')
    </section>

    <section class="view">
        {!! form()->open(['url' => route('admin.admin.groups.store'), 'method' => 'POST', 'class' => 'form']) !!}
            @include('admin.admin.groups._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.admin.groups.index') !!}
    </section>
    <section class="actions">
        <a class="btn dark-blue duplicate">
            <i class="fa fa-files-o"></i>&nbsp; Duplicate
        </a>
        <a class="btn yellow preview">
            <i class="fa fa-eye"></i>&nbsp; Preview
        </a>
        <a class="btn red draft">
            <i class="fa fa-cloud"></i>&nbsp; Save as Draft
        </a>
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection