@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.pages._tabs', ['on_limbo_draft' => true])
    </section>

    <section class="view">
        @include('admin.cms.pages._form', ['on_limbo_draft' => true])
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.pages.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}
        {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
    </section>
@endsection