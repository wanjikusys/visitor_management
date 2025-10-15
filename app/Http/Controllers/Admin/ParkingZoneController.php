<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingZone;
use Illuminate\Http\Request;

class ParkingZoneController extends Controller
{
    public function index()
    {
        $zones = ParkingZone::withCount(['vehicleVisits' => function ($query) {
            $query->where('status', 'parked');
        }])->get();

        return view('admin.parking-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.parking-zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:parking_zones,code',
            'description' => 'nullable|string',
            'total_slots' => 'required|integer|min:1',
            'zone_type' => 'required|in:visitor,vip,staff,loading,disabled,motorcycle',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['available_slots'] = $validated['total_slots'];
        $validated['is_active'] = true;

        $zone = ParkingZone::create($validated);

        return redirect()->route('admin.parking-zones.index')
            ->with('success', 'Parking zone created successfully!');
    }

    public function show(ParkingZone $parkingZone)
    {
        $parkingZone->load(['vehicleVisits.vehicle', 'vehicleVisits.visitorVisit.visitor']);
        
        $currentVehicles = $parkingZone->vehicleVisits()
            ->where('status', 'parked')
            ->with(['vehicle', 'visitorVisit.visitor'])
            ->latest('entry_time')
            ->get();

        return view('admin.parking-zones.show', compact('parkingZone', 'currentVehicles'));
    }

    public function edit(ParkingZone $parkingZone)
    {
        return view('admin.parking-zones.edit', compact('parkingZone'));
    }

    public function update(Request $request, ParkingZone $parkingZone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:parking_zones,code,' . $parkingZone->id,
            'description' => 'nullable|string',
            'total_slots' => 'required|integer|min:1',
            'zone_type' => 'required|in:visitor,vip,staff,loading,disabled,motorcycle',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        // Adjust available slots if total changed
        if ($validated['total_slots'] != $parkingZone->total_slots) {
            $difference = $validated['total_slots'] - $parkingZone->total_slots;
            $validated['available_slots'] = max(0, $parkingZone->available_slots + $difference);
        }

        $parkingZone->update($validated);

        return redirect()->route('admin.parking-zones.show', $parkingZone)
            ->with('success', 'Parking zone updated successfully!');
    }

    public function destroy(ParkingZone $parkingZone)
    {
        if ($parkingZone->vehicleVisits()->where('status', 'parked')->exists()) {
            return redirect()->back()->with('error', 'Cannot delete parking zone with active vehicles.');
        }

        $parkingZone->delete();

        return redirect()->route('admin.parking-zones.index')
            ->with('success', 'Parking zone deleted successfully!');
    }
}
