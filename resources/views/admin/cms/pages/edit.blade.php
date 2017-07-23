@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.pages._tabs')
    </section>

    <section class="view">
        @include('admin.cms.pages._form', ['url' => route('admin.pages.update', ['id' => $item->id])])
    </section>
@endsection

@section('footer')
    @include('admin.cms.pages._buttons')
@endsection

