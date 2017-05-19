@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Edit Admin User</h1>
@endsection

@section('content')
    <section class="tabs">
        @include('admin.acl.admin_users._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.admin_users.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.admin_users._form')
        {!! form_admin()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.admin_users.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection

