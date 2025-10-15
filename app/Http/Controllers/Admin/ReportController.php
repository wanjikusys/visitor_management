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

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function visitors(Request $request)
    {
        $query = VisitorVisit::with(['visitor', 'host', 'vehicleVisit.vehicle']);

        if ($request->filled('start_date')) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('check_in_time', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->latest('check_in_time')->paginate(50);

        $stats = [
            'total_visits' => $visits->total(),
            'active_visits' => VisitorVisit::where('status', 'active')->count(),
            'completed_visits' => $query->where('status', 'completed')->count(),
            'average_duration' => $this->calculateAverageDuration($request),
        ];

        return view('admin.reports.visitors', compact('visits', 'stats'));
    }

    public function vehicles(Request $request)
    {
        $query = VehicleVisit::with(['vehicle', 'visitorVisit.visitor', 'parkingZone']);

        if ($request->filled('start_date')) {
            $query->whereDate('entry_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('entry_time', '<=', $request->end_date);
        }

        $vehicleVisits = $query->latest('entry_time')->paginate(50);

        $stats = [
            'total_vehicles' => $vehicleVisits->total(),
            'currently_parked' => VehicleVisit::where('status', 'parked')->count(),
            'average_parking_time' => $this->calculateAverageParkingTime($request),
        ];

        return view('admin.reports.vehicles', compact('vehicleVisits', 'stats'));
    }

    public function assets(Request $request)
    {
        $query = AssetCheckout::with(['asset', 'user', 'visitorVisit.visitor', 'checkedOutBy', 'returnedBy']);

        if ($request->filled('start_date')) {
            $query->whereDate('checkout_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('checkout_time', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $checkouts = $query->latest('checkout_time')->paginate(50);

        $stats = [
            'total_checkouts' => $checkouts->total(),
            'currently_checked_out' => AssetCheckout::where('status', 'checked_out')->count(),
            'overdue_checkouts' => AssetCheckout::where('status', 'checked_out')
                ->where('expected_return_time', '<', now())
                ->count(),
            'total_assets' => Asset::count(),
            'available_assets' => Asset::where('status', 'available')->count(),
        ];

        return view('admin.reports.assets', compact('checkouts', 'stats'));
    }

    private function calculateAverageDuration($request)
    {
        $query = VisitorVisit::where('status', 'completed')
            ->whereNotNull('actual_checkout_time');

        if ($request->filled('start_date')) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('check_in_time', '<=', $request->end_date);
        }

        $visits = $query->get();

        if ($visits->isEmpty()) {
            return 0;
        }

        $totalMinutes = $visits->sum(function ($visit) {
            return $visit->actual_checkout_time->diffInMinutes($visit->check_in_time);
        });

        return round($totalMinutes / $visits->count());
    }

    private function calculateAverageParkingTime($request)
    {
        $query = VehicleVisit::where('status', 'exited')
            ->whereNotNull('exit_time');

        if ($request->filled('start_date')) {
            $query->whereDate('entry_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('entry_time', '<=', $request->end_date);
        }

        $visits = $query->get();

        if ($visits->isEmpty()) {
            return 0;
        }

        $totalMinutes = $visits->sum('duration_minutes');

        return round($totalMinutes / $visits->count());
    }
}
