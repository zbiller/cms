@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.layouts._tabs')
    </section>

    <section class="view">
        @include('admin.cms.layouts._form', ['url' => route('admin.layouts.store')])
    </section>
@endsection

@section('footer')
    @include('admin.cms.layouts._buttons')
@endsection