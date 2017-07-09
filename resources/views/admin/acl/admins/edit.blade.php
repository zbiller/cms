@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.acl.admins._tabs')
    </section>

    <section class="view">
        {!! form_admin()->model($item, ['url' => route('admin.admins.update', ['id' => $item->id]), 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
            @include('admin.acl.admins._form')
        {!! form_admin()->close() !!}
</section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.admins.index')) !!}
    </section>
    <section class="actions">
        {!! button()->saveAndStay() !!}
        {!! button()->saveRecord() !!}
    </section>
@endsection

