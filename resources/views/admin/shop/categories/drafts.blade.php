@extends('layouts::admin.default')

@section('content')
    <section class="filters">
        @include('admin.shop.categories._filter')
    </section>

    <section class="list">
        <table class="categories-table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <td class="sortable" data-sort="name">
                        <i class="fa fa-sort"></i>&nbsp; Name
                    </td>
                    <td class="sortable" data-sort="slug">
                        <i class="fa fa-sort"></i>&nbsp; URL
                    </td>
                    <td class="sortable" data-sort="active">
                        <i class="fa fa-sort"></i>&nbsp; Active
                    </td>
                    <td class="actions-drafted">Actions</td>
                </tr>
            </thead>
            <tbody>
            @if($items->count() > 0)
                @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->name ?: 'N/A' }}</td>
                        <td>{{ optional($item->url)->url ?: 'N/A' }}</td>
                        <td>{{ $actives[$item->active] ?? 'N/A' }}</td>
                        <td>
                            {!! button()->publishLimboDraft(route('admin.drafts.publish_limbo'), $item) !!}
                            {!! button()->editRecord(route('admin.product_categories.limbo', $item->id)) !!}
                            {!! button()->deleteLimboDraft(route('admin.drafts.delete_limbo'), $item) !!}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10">No records found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </section>
@endsection

@section('footer')
    {!! pagination('admin')->render($items) !!}

    <section class="actions">
        {!! button()->goBack(route('admin.product_categories.index')) !!}
    </section>
@endsection