@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.acl.roles._tabs')
    </section>

    <section class="view">
        @include('admin.acl.roles._form', ['url' => route('admin.roles.update', $item->id)])
</section>
@endsection

@section('footer')
    @include('admin.acl.roles._buttons')
@endsection

