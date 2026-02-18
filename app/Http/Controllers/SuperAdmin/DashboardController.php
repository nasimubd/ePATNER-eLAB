<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('is_active', true)->count(),
            'inactive_businesses' => Business::where('is_active', false)->count(),
            'total_users' => User::count(),
        ];

        $recent_businesses = Business::latest()->take(5)->get();

        return view('super-admin.dashboard', compact('stats', 'recent_businesses'));
    }
}
