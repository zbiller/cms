@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.localisation.countries._tabs')
    </section>

    <section class="view">
        @include('admin.localisation.countries._form', ['url' => route('admin.countries.update', $item->id)])
    </section>
@endsection

@section('footer')
    @include('admin.localisation.countries._buttons')
@endsection

