{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('layout', ['' => 'All Layouts'] + $layouts->pluck('name', 'id')->toArray(), request('layout') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('type', ['' => 'All Types'] + $types, request('type') ?: null) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('active', ['' => 'Active'] + $actives, request('active') ?: null) !!}
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