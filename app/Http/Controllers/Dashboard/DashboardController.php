<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Fetch real data from database
        $stats = [
            [
                'label' => 'Service Requests',
                'value' => \App\Models\ServiceRequest::count(),
                'icon' => 'bi-clipboard-data',
                'tone' => 'primary',
            ],
            [
                'label' => 'Pending',
                'value' => \App\Models\ServiceRequest::where('status', 'pending')->count(),
                'icon' => 'bi-clock-history',
                'tone' => 'warning',
            ],
            [
                'label' => 'Completed',
                'value' => \App\Models\ServiceRequest::where('status', 'completed')->count(),
                'icon' => 'bi-check-circle',
                'tone' => 'success',
            ],
            [
                'label' => 'In Progress',
                'value' => \App\Models\ServiceRequest::where('status', 'in_progress')->count(),
                'icon' => 'bi-arrow-repeat',
                'tone' => 'info',
            ],
        ];

        $summaryTiles = [
            [
                'label' => 'Total Users',
                'value' => \App\Models\User::count(),
                'icon' => 'bi-people',
            ],
            [
                'label' => 'Active Services',
                'value' => \App\Models\Service::count(),
                'icon' => 'bi-gear',
            ],
            [
                'label' => 'Revenue',
                'value' => '$' . number_format(\App\Models\ServiceRequest::where('status', 'completed')->sum('cost') ?? 0, 2),
                'icon' => 'bi-currency-dollar',
            ],
            [
                'label' => 'System Status',
                'value' => 'Online',
                'icon' => 'bi-wifi',
            ],
        ];

        return view('dashboard.dashboard', compact('stats', 'summaryTiles'));
    }
}
