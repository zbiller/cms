@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.location.countries._tabs')
    </section>

    <section class="view">
        @include('admin.location.countries._form', ['url' => route('admin.countries.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.location.countries._buttons')
@endsection

