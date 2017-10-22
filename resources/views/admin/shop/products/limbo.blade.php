@extends('layouts::admin.default')

@section('content')
    <section class="tabs">
        @include('admin.shop.products._tabs', ['on_limbo_draft' => true])
    </section>

    <section class="view">
        @include('admin.shop.products._form', ['on_limbo_draft' => true])
    </section>
@endsection

@section('footer')
    <section class="actions left">
        {!! button()->cancelAction(route('admin.products.drafts')) !!}
    </section>
    <section class="actions">
        {!! button()->saveRecord(['style' => 'margin-right: 5px;']) !!}

        @permission('drafts-publish')
            {!! button()->publishDraft(route('admin.drafts.publish_limbo')) !!}
        @else
            {!! button()->saveForApproval(route('admin.drafts.approval'), route('admin.products.limbo', $item->id)) !!}
        @endpermission
    </section>
@endsection