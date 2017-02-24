<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\AdminController;

class DashboardController extends AdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.home.dashboard');
    }
}