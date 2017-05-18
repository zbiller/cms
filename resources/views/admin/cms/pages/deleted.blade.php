@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Pages</h1>
@endsection

@section('content')
    <section class="filters">
        @include('admin.cms.pages._filter')
    </section>

    <section class="list">
        <table class="pages-table" cellspacing="0" cellpadding="0" border="0">
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
                    <td class="actions-deleted">Actions</td>
                </tr>
            </thead>
            <tbody>
            @if($items->count() > 0)
                @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->name ?: 'N/A' }}</td>
                        <td>{{ $item->url ? $item->url->url : 'N/A' }}</td>
                        <td>{{ isset($actives[$item->active]) ? $actives[$item->active] : 'N/A' }}</td>
                        <td>
                            {!! button()->restore(route('admin.pages.restore', $item->id)) !!}
                            {!! button()->delete(route('admin.pages.delete', $item->id)) !!}
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
        {!! button()->update() !!}
        {!! button()->action('Go Back', route('admin.pages.index'), 'fa-chevron-left', 'blue') !!}
    </section>
@endsection