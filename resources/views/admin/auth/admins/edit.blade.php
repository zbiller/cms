@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.auth.admins._tabs')
    </section>

    <section class="view">
        @include('admin.auth.admins._form', ['url' => route('admin.admins.update', $item->id)])
</section>
@endsection

@section('footer')
    @include('admin.auth.admins._buttons')
@endsection

