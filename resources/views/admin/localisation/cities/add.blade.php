@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.localisation.cities._tabs')
    </section>

    <section class="view">
        @include('admin.localisation.cities._form', ['url' => route('admin.cities.store')])
    </section>
@endsection

@section('footer')
    @include('admin.localisation.cities._buttons')
@endsection