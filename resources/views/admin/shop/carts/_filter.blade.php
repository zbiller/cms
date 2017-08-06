{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('user', ['' => 'All Users', null => 'Guests only'] + $users->pluck('full_name', 'id')->toArray(), request('user') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->text('total[0]', request('total')[0] ?: null, ['placeholder' => 'Total From', 'style' => 'width: 48%;']) !!}
        {!! form()->text('total[1]', request('total')[1] ?: null, ['placeholder' => 'Total To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->text('count[0]', request('count')[0] ?: null, ['placeholder' => 'Count From', 'style' => 'width: 48%;']) !!}
        {!! form()->text('count[1]', request('count')[1] ?: null, ['placeholder' => 'Count To', 'style' => 'width: 48%;']) !!}
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