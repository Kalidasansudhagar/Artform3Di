<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDemo;
use Illuminate\View\View;

/**
 * Admin dashboard (frontend preview). Uses sample data from AdminDemo and has no
 * authentication yet — add the `auth` + admin-role middleware to the route group
 * in the backend phase.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', AdminDemo::dashboard());
    }
}
