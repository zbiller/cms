@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.translation.translations._tabs')
    </section>

    <section class="view">
        @include('admin.translation.translations._form', ['url' => route('admin.translations.update', $item->id)])
    </section>
@endsection

@section('footer')
    @include('admin.translation.translations._buttons')
@endsection

