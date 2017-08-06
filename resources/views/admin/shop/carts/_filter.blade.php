{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('user', ['' => 'All Users', 'guests_only' => 'Guests only'] + $users->pluck('full_name', 'id')->toArray(), request('user') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('total_from', request('total_from') ?: null, ['placeholder' => 'Total From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('total_to', request('total_to') ?: null, ['placeholder' => 'Total To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('count_from', request('count_from') ?: null, ['placeholder' => 'Count From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('count_to', request('count_to') ?: null, ['placeholder' => 'Count To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form_admin()->calendar('start_date', false, request('start_date') !== null ? request('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 48%;']) !!}
        {!! form_admin()->calendar('end_date', false, request('end_date') !== null ? request('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <div>
        {!! button()->filterRecords() !!}
        {!! button()->clearFilters() !!}
    </div>
{!! form()->close() !!}