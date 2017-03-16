@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Admin Role</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.admin_roles._tabs')
    </section>

    <section class="view">
        {!! adminform()->model($item, ['url' => route('admin.admin_roles.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.admin_roles._form')
        {!! form()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.admin_roles.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection

