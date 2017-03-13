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
        {!! adminform()->model($item, ['url' => route('admin.admin_users.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form']) !!}
            @include('admin.acl.admin_users._form')
        {!! adminform()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancel('admin.admin_users.index') !!}
    </section>
    <section class="actions">
        {!! button()->saveStay() !!}
        {!! button()->save() !!}
    </section>
@endsection

