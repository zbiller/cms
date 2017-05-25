<?php

namespace App\Http\Controllers\Admin\Home;

use Analytics;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Analytics\Period;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        if ($request->has('start_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->get('start_date'));
        }

        if ($request->has('end_date')) {
            $endDate = Carbon::createFromFormat('Y-m-d', $request->get('end_date'));
        }

        if ($startDate > $endDate) {
            session()->flash('flash_error', 'Please select a valid time period in which to view Analytics data!');
            return back();
        }

        $analytics = $this->formatAnalyticsData($this->fetchAnalyticsData($startDate, $endDate));

        return view('admin.home.dashboard')->with([
            'analytics' => $analytics
        ]);
    }

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    protected function fetchAnalyticsData(Carbon $startDate, Carbon $endDate)
    {
        $response = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:users,ga:pageviews,ga:sessions,ga:exits',
            [
                'dimensions' => 'ga:date'
            ]
        );

        return collect($response['rows'] ?? [])->map(function (array $row) {
            return [
                'date' => Carbon::createFromFormat('Ymd', $row[0])->format('d M Y'),
                'visitors' => $row[1],
                'page_views' => (int)$row[2],
                'sessions' => (int)$row[3],
                'exits' => (int)$row[3],
            ];
        });
    }

    /**
     * @param Collection $data
     * @return string
     */
    public function formatAnalyticsData(Collection $data)
    {
        $format = [];
        $format['cols'] = [
            ['label' => '', 'type' => 'string'],
            ['label' => 'Visitors', 'type' => 'number'],
            ['label' => 'Page Views', 'type' => 'number'],
            ['label' => 'Sessions', 'type' => 'number'],
            ['label' => 'Exits', 'type' => 'number'],
        ];

        foreach ($data as $index => $col) {
            $format['rows'][$index]['c'] = [
                ['v' => $col['date']],
                ['v' => $col['visitors']],
                ['v' => $col['page_views']],
                ['v' => $col['sessions']],
                ['v' => $col['exits']],
            ];
        }

        return json_encode($format);
    }
}