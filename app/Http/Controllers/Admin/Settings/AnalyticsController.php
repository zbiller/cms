<?php

namespace App\Http\Controllers\Admin\Settings;

use DB;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Config\Setting;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $this->setMeta('title', 'Admin - Settings - Analytics');

        switch (strtolower($request->method())) {
            case 'get':
                return $this->get();
                break;
            case 'post':
                return $this->post($request);
                break;
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function get()
    {
        return view('admin.settings.analytics');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                foreach ($request->except('_token') as $key => $value) {
                    $data = [
                        'key' => $key,
                        'value' => $value,
                    ];

                    if ($model = setting()->find($key)) {
                        $model->update($data);
                    } else {
                        Setting::create($data);
                    }
                }

                session()->flash('flash_success', __('crud.update_success'));
                return redirect()->route('admin.settings.analytics');
            });
        } catch (Exception $e) {
            session()->flash('flash_success', 'Something went wrong! Please try again.');
            return redirect()->route('admin.settings.analytics');
        }
    }
}