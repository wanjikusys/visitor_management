<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use App\Models\VehicleVisit;
use App\Models\ParkingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicVisitorController extends Controller
{
    public function create()
    {
        $hosts = User::where('is_active', true)->orderBy('name')->get();
        $parkingZones = ParkingZone::where('is_active', true)
            ->where('available_slots', '>', 0)
            ->where('zone_type', 'visitor')
            ->get();

        return view('public.visitor-register', compact('hosts', 'parkingZones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'id_type' => 'required|in:national_id,passport,driving_license,other',
            'photo' => 'nullable|image|max:2048',
            'host_id' => 'required|exists:users,id',
            'visit_purpose' => 'required|string|max:255',
            'temperature' => 'nullable|numeric',
            'has_vehicle' => 'nullable|boolean',
            'plate_number' => 'required_if:has_vehicle,true|nullable|string',
            'vehicle_type' => 'nullable|in:car,motorcycle,truck,van,suv,bus,bicycle,other',
        ]);

        // Create or find visitor
        $visitor = Visitor::firstOrCreate(
            ['id_number' => $validated['id_number']],
            [
                'full_name' => $validated['full_name'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'] ?? null,
                'company' => $validated['company'] ?? null,
                'id_type' => $validated['id_type'],
            ]
        );

        // Handle photo
        if ($request->hasFile('photo')) {
            $visitor->photo_path = $request->file('photo')->store('visitors', 'public');
            $visitor->save();
        }

        // Check blacklist
        if ($visitor->is_blacklisted) {
            return redirect()->back()->with('error', 'Your access has been denied. Please contact reception.');
        }

        // Create visit
        $visit = VisitorVisit::create([
            'visitor_id' => $visitor->id,
            'host_id' => $validated['host_id'],
            'visit_purpose' => $validated['visit_purpose'],
            'check_in_time' => now(),
            'temperature' => $validated['temperature'] ?? null,
            'badge_number' => 'BADGE-' . str_pad($visitor->id, 6, '0', STR_PAD_LEFT),
            'qr_code' => Str::uuid(),
            'status' => 'active',
        ]);

        // Handle vehicle
        if ($request->has_vehicle && $request->filled('plate_number')) {
            $vehicle = Vehicle::firstOrCreate(
                ['plate_number' => strtoupper($validated['plate_number'])],
                ['vehicle_type' => $validated['vehicle_type'] ?? 'car']
            );

            if (!$vehicle->is_blacklisted) {
                // Find available parking
                $parkingZone = ParkingZone::where('is_active', true)
                    ->where('zone_type', 'visitor')
                    ->where('available_slots', '>', 0)
                    ->first();

                VehicleVisit::create([
                    'vehicle_id' => $vehicle->id,
                    'visitor_visit_id' => $visit->id,
                    'parking_zone_id' => $parkingZone?->id,
                    'entry_time' => now(),
                    'status' => 'parked',
                ]);

                $parkingZone?->decrement('available_slots');
            }
        }

        return view('public.visitor-success', compact('visit'));
    }
}
