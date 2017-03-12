@extends('layouts::admin.default')

@section('header')
    @parent

    <h1>Admin Groups</h1>
@endsection

@section('content')
    @include('admin.admin.users._filter')

    <section class="list">
        <table cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <td class="sortable" data-sort="name">
                        <i class="fa fa-sort"></i>&nbsp; Username
                    </td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                @if($items->count() > 0)
                    @foreach($items as $index => $item)
                    <tr class="{!! $index % 2 == 0 ? 'even' : 'odd' !!}">
                        <td>{{ $item->username }}</td>
                        <td>
                            {!! button()->edit('admin.admin.users.edit', ['id' => $item->id]) !!}
                            {!! button()->delete('admin.admin.users.destroy', ['id' => $item->id]) !!}
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
    {!! pagination()->render($items, 'admin') !!}

    <section class="actions">
        {!! button()->update() !!}
        {!! button()->add('admin.admin.users.create') !!}
    </section>
@endsection