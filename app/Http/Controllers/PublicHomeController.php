<?php

namespace App\Http\Controllers;

use App\Models\VisitorVisit;
use App\Models\Vehicle;
use App\Models\Asset;
use Illuminate\Http\Request;

class PublicHomeController extends Controller
{
    public function index()
    {
        $stats = [
            'active_visitors' => VisitorVisit::where('status', 'active')->count(),
            'total_today' => VisitorVisit::whereDate('check_in_time', today())->count(),
            'parked_vehicles' => Vehicle::whereHas('currentVisit')->count(),
        ];

        return view('public.home', compact('stats'));
    }
}
