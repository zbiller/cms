@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Admin Role</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.test.cars._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.cars.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.test.cars._form')
        {!! form()->close() !!}
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.cars.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->save() !!}
    </section>
@endsection

