@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.categories._tabs', ['on_limbo_draft' => true])
    </section>

    <section class="view">
        @include('admin.shop.categories._form', ['on_limbo_draft' => true])
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.product_categories.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}
        {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
    </section>
@endsection