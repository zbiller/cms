@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.localisation.currencies._tabs')
    </section>

    <section class="view">
        @include('admin.localisation.currencies._form', ['url' => route('admin.currencies.update', $item->id)])
    </section>
@endsection

@section('footer')
    @include('admin.localisation.currencies._buttons')
@endsection

