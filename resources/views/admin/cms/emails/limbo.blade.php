@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.emails._tabs', ['on_limbo_draft' => true])
    </section>

    <section class="view">
        @include('admin.cms.emails._form', ['on_limbo_draft' => true])
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.emails.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}
        {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
    </section>
@endsection

