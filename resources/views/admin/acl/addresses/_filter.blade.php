{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->query('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('country', ['' => 'All countries'] + $countries->pluck('name', 'id')->toArray(), request()->query('country') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form_admin()->calendar('start_date', false, request()->query('start_date') !== null ? request()->query('start_date') : null, ['placeholder' => 'Date From', 'style' => 'width: 48%;']) !!}
        {!! form_admin()->calendar('end_date', false, request()->query('end_date') !== null ? request()->query('end_date') : null, ['placeholder' => 'Date To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <div>
        {!! button()->filterRecords() !!}
        {!! button()->clearFilters() !!}
    </div>
{!! form()->close() !!}