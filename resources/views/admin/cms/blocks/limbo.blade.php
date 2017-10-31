@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.cms.blocks._tabs', ['on_limbo_draft' => true])
    </section>

    <section class="view">
        @include('admin.cms.blocks._form', ['on_limbo_draft' => true])
    </section>
@endsection

@section('footer')
    <section class="left">
        {!! button()->cancelAction(route('admin.blocks.drafts')) !!}
    </section>
    <section class="right">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}

        @permission('drafts-publish')
            {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
        @else
            {!! button()->saveForApproval(route('admin.drafts.approval'), route('admin.blocks.limbo', $item->id)) !!}
        @endpermission
    </section>
@endsection

