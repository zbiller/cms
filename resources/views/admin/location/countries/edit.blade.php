@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>{{ $title }}</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.location.countries._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.countries.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.location.countries._form')
        {!! form()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.countries.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection

