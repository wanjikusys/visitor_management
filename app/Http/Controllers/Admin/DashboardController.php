<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCheckout;
use App\Models\Vehicle;
use App\Models\VehicleVisit;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_visitors' => VisitorVisit::where('status', 'active')->count(),
            'total_visitors_today' => VisitorVisit::whereDate('check_in_time', today())->count(),
            'active_vehicles' => VehicleVisit::where('status', 'parked')->count(),
            'available_assets' => Asset::where('status', 'available')->count(),
            'checked_out_assets' => Asset::where('status', 'checked_out')->count(),
            'overdue_assets' => AssetCheckout::where('status', 'checked_out')
                ->where('expected_return_time', '<', now())
                ->count(),
        ];

        $recentVisitors = VisitorVisit::with(['visitor', 'host'])
            ->latest('check_in_time')
            ->take(10)
            ->get();

        $activeVehicles = VehicleVisit::with(['vehicle', 'parkingZone'])
            ->where('status', 'parked')
            ->latest('entry_time')
            ->get();

        $overdueCheckouts = AssetCheckout::with(['asset', 'user', 'visitorVisit.visitor'])
            ->where('status', 'checked_out')
            ->where('expected_return_time', '<', now())
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentVisitors', 'activeVehicles', 'overdueCheckouts'));
    }
}
