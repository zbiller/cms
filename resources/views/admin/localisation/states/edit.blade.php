@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.localisation.states._tabs')
    </section>

    <section class="view">
        @include('admin.localisation.states._form', ['url' => route('admin.states.update', $item->id)])
</section>
@endsection

@section('footer')
    @include('admin.localisation.states._buttons')
@endsection

