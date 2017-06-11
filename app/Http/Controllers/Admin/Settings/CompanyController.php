<?php

namespace App\Http\Controllers\Admin\Settings;

use DB;
use Validator;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Config\Setting;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $this->setMeta('title', 'Admin - Settings - Company');

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
        return view('admin.settings.company');
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

                    $validator = Validator::make(
                        $data, ['value' => 'required'], [],
                        ['value' => title_case(str_replace('-', ' ', $key))]
                    );

                    if ($validator->fails()) {
                        return redirect()->route('admin.settings.company')->withErrors($validator->errors());
                    }

                    if ($model = setting()->find($key)) {
                        $model->update($data);
                    } else {
                        Setting::create($data);
                    }
                }

                session()->flash('flash_success', __('crud.update_success'));
                return redirect()->route('admin.settings.company');
            });
        } catch (Exception $e) {
            session()->flash('flash_success', 'Something went wrong! Please try again.');
            return redirect()->route('admin.settings.company');
        }
    }
}