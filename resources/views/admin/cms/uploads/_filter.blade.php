{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', ['' => 'All Types'] + $types, request('type') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('size[0]', request('size')[0] ?: null, ['placeholder' => 'Size From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('size[1]', request('size')[1] ?: null, ['placeholder' => 'Size To', 'style' => 'width: 48%;']) !!}
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