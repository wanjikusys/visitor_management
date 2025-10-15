<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleVisit;
use App\Models\VehicleTrackingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('vehicle_type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'parked') {
                $query->whereHas('currentVisit');
            }
        }

        $vehicles = $query->latest()->paginate(20);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'vehicle_type' => 'required|in:car,motorcycle,truck,van,suv,bus,bicycle,other',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['plate_number'] = strtoupper($validated['plate_number']);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        // Remove photo from array before creating
        unset($validated['photo']);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Vehicle registered successfully!');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['vehicleVisits.visitorVisit.visitor', 'vehicleVisits.parkingZone']);
        
        $currentVisit = $vehicle->currentVisit;
        $visitHistory = $vehicle->vehicleVisits()
            ->with(['visitorVisit.visitor', 'parkingZone'])
            ->latest('entry_time')
            ->paginate(10);

        return view('admin.vehicles.show', compact('vehicle', 'currentVisit', 'visitHistory'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'vehicle_type' => 'required|in:car,motorcycle,truck,van,suv,bus,bicycle,other',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['plate_number'] = strtoupper($validated['plate_number']);

        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        // Remove photo from array
        unset($validated['photo']);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    public function tracking(Vehicle $vehicle)
    {
        $currentVisit = $vehicle->currentVisit;
        
        if (!$currentVisit) {
            return redirect()->back()->with('error', 'Vehicle is not currently parked.');
        }

        $trackingLogs = $currentVisit->trackingLogs()
            ->with('user')
            ->latest('event_time')
            ->get();

        return view('admin.vehicles.tracking', compact('vehicle', 'currentVisit', 'trackingLogs'));
    }

    public function blacklist(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'blacklist_reason' => 'required|string',
        ]);

        $vehicle->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $validated['blacklist_reason'],
            'blacklisted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Vehicle blacklisted successfully.');
    }
}
