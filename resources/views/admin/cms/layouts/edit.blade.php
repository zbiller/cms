@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Layout</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.cms.layouts._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.layouts.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.cms.layouts._form')
        {!! form()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel(route('admin.layouts.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->save() !!}
    </section>
@endsection

