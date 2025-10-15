<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use App\Models\Hmis\HmisVehicle;
use Illuminate\Http\Request;

class HmisVehiclesController extends Controller
{
    public function index()
    {
        return view('hmis.vehicles.index');
    }

    public function getCheckedIn()
    {
        try {
            $vehicles = HmisVehicle::where('checked_in', true)
                ->orderBy('card_no')
                ->get();

            return response()->json([
                'success' => true,
                'vehicles' => $vehicles,
            ]);
        } catch (\Exception $e) {
            \Log::error('HMIS Vehicles Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'card_no' => 'required|string|max:50',
                'driver_name' => 'required|string|max:255',
                'registration' => 'required|string|max:50',
                'phone_number' => 'required|string|max:20',
                'visit_purpose' => 'required|string',
                'passengers' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            $vehicle = HmisVehicle::create([
                'card_no' => $validated['card_no'],
                'driver_name' => $validated['driver_name'],
                'registration' => $validated['registration'],
                'phone_number' => $validated['phone_number'],
                'visit_purpose' => $validated['visit_purpose'],
                'passengers' => $validated['passengers'],
                'notes' => $validated['notes'] ?? null,
                'time_in' => now(),
                'checked_in' => true,
                'checked_in_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle checked in successfully',
                'vehicle' => $vehicle,
            ]);
        } catch (\Exception $e) {
            \Log::error('HMIS Vehicle Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $vehicle = HmisVehicle::findOrFail($id);
            return response()->json([
                'success' => true,
                'vehicle' => $vehicle,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $vehicle = HmisVehicle::findOrFail($id);

            $validated = $request->validate([
                'card_no' => 'required|string|max:50',
                'driver_name' => 'required|string|max:255',
                'registration' => 'required|string|max:50',
                'phone_number' => 'required|string|max:20',
                'visit_purpose' => 'required|string',
                'passengers' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            $vehicle->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle updated successfully',
                'vehicle' => $vehicle,
            ]);
        } catch (\Exception $e) {
            \Log::error('HMIS Vehicle Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkout($id)
    {
        try {
            $vehicle = HmisVehicle::findOrFail($id);

            $vehicle->update([
                'time_out' => now(),
                'checked_in' => false,
                'checked_out_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle checked out successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('HMIS Vehicle Checkout Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
