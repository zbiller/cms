@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.location.states._tabs')
    </section>

    <section class="view">
        @include('admin.location.states._form', ['url' => route('admin.states.update', ['id' => $item->id])])
</section>
@endsection

@section('footer')
    @include('admin.location.states._buttons')
@endsection

