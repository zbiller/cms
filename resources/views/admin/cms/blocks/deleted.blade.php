@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Pages</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.blocks._filter')
    </section>

    <section class="list">
        <table class="pages-table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <td class="sortable" data-sort="name">
                        <i class="fa fa-sort"></i>&nbsp; Name
                    </td>
                    <td class="sortable" data-sort="type">
                        <i class="fa fa-sort"></i>&nbsp; Type
                    </td>
                    <td class="actions-deleted">Actions</td>
                </tr>
            </thead>
            <tbody>
            @if($items->count() > 0)
                @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->name ?: 'N/A' }}</td>
                        <td>{{ isset($types[$item->type]) ? $types[$item->type] : 'N/A' }}</td>
                        <td>
                            {!! button()->restoreRecord(route('admin.blocks.restore', $item->id)) !!}
                            {!! button()->deleteRecord(route('admin.blocks.delete', $item->id)) !!}
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
        {!! button()->goBack(route('admin.blocks.index')) !!}
    </section>
@endsection