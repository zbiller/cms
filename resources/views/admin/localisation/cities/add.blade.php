@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.location.cities._tabs')
    </section>

    <section class="view">
        @include('admin.location.cities._form', ['url' => route('admin.cities.store')])
    </section>
@endsection

@section('footer')
    @include('admin.location.cities._buttons')
@endsection