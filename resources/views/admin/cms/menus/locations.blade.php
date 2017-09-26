@extends('layouts::admin.default')

@section('content')
    <section class="list">
        <table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <td>Location</td>
                </tr>
            </thead>
            <tbody>
                @if(isset($locations) && !empty($locations))
                    @foreach($locations as $location => $name)
                        <tr class="{!! $loop->index % 2 == 0 ? 'even' : 'odd' !!}">
                            <td>
                                <a href="{{ route('admin.menus.index', $location) }}">{{ $name ?: 'N/A' }}</a>
                                (click to view the menu items from this location)
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
    <section class="actions">
        {!! button()->updateAction() !!}
    </section>
@endsection