@extends('layouts::admin.default')

@section('content')
    <section id="analytics" class="view">
        <span class="section-title">Analytics Metrics</span>

        @if(isset($analytics) && $analytics)
            <section class="filters filters-inside">
                {!! form()->open(['url' => request()->url(), 'method' => 'GET']) !!}
                    <fieldset>
                        {!! form_admin()->calendar('start_date', false, request('start_date') !== null ? request('start_date') : null, ['placeholder' => 'Date From']) !!}
                    </fieldset>
                    <fieldset>
                        {!! form_admin()->calendar('end_date', false, request('end_date') !== null ? request('end_date') : null, ['placeholder' => 'Date To']) !!}
                    </fieldset>
                    <div>
                        {!! button()->filterRecords() !!}
                        {!! button()->clearFilters() !!}
                    </div>
                {!! form()->close() !!}
            </section>
            <div id="analytics-chart"></div>
        @else
            <span>No data to show because the Google Analytics is not configured within the App.</span>
        @endif
    </section>
@endsection

@section('footer')
    <section class="actions">
        {!! button()->updatePage(['style' => 'margin-right: 0;']) !!}
    </section>
@endsection

@section('bottom_scripts')
    @if(isset($analytics) && $analytics)
        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages':['line']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var chart = new google.charts.Line(document.getElementById('analytics-chart'));
                var data = new google.visualization.DataTable({!! $analytics !!});

                var options = {
                    width: '100%',
                    height: 400,
                    colors: [
                        '#4AAEE3',
                        '#55D98D',
                        '#DD7467',
                        '#FFC65D'
                    ]
                };

                chart.draw(data, options);
            }
        </script>
    @endif
@append