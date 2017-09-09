@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.localisation.languages._tabs')
    </section>

    <section class="view">
        @include('admin.localisation.languages._form', ['url' => route('admin.languages.update', $item->id)])
    </section>
@endsection

@section('footer')
    @include('admin.localisation.languages._buttons')
@endsection

