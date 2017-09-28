@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.seo.redirects._tabs')
    </section>

    <section class="view">
        @include('admin.seo.redirects._form', ['url' => route('admin.redirects.store')])
    </section>
@endsection

@section('footer')
    @include('admin.seo.redirects._buttons')
@endsection