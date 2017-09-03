{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->query('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('total[0]', request()->query('total')[0] ?: null, ['placeholder' => 'Total From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('total[1]', request()->query('total')[1] ?: null, ['placeholder' => 'Total To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('status', ['' => 'Status'] + $statuses, request()->query('status') ?: null, ['style' => 'width: 48%;']) !!}
        {!! form()->select('viewed', ['' => 'Viewed'] + $views, request()->query('viewed') ?: null, ['style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('payment', ['' => 'Payment'] + $payments, request()->query('payment') ?: null, ['style' => 'width: 48%;']) !!}
        {!! form()->select('shipping', ['' => 'Shipping'] + $shippings, request()->query('shipping') ?: null, ['style' => 'width: 48%;']) !!}
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