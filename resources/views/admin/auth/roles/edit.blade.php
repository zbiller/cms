@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.auth.roles._tabs')
    </section>

    <section class="view">
        @include('admin.auth.roles._form', ['url' => route('admin.roles.update', $item->id)])
</section>
@endsection

@section('footer')
    @include('admin.auth.roles._buttons')
@endsection

