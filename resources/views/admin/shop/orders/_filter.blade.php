{!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
    <fieldset>
        {!! form()->text('search', request()->get('search') ?: null, ['placeholder' => 'Search']) !!}
    </fieldset>
    <fieldset>
        {!! form()->number('total[0]', request('total')[0] ?: null, ['placeholder' => 'Total From', 'style' => 'width: 48%;']) !!}
        {!! form()->number('total[1]', request('total')[1] ?: null, ['placeholder' => 'Total To', 'style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('status', ['' => 'Status'] + $statuses, request('status') ?: null, ['style' => 'width: 48%;']) !!}
        {!! form()->select('viewed', ['' => 'Viewed'] + $views, request('viewed') ?: null, ['style' => 'width: 48%;']) !!}
    </fieldset>
    <fieldset>
        {!! form()->select('payment', ['' => 'Payment'] + $payments, request('payment') ?: null, ['style' => 'width: 48%;']) !!}
        {!! form()->select('shipping', ['' => 'Shipping'] + $shippings, request('shipping') ?: null, ['style' => 'width: 48%;']) !!}
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