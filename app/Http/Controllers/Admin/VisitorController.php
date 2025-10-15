<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\ParkingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitorVisit::with(['visitor', 'host', 'vehicleVisit.vehicle']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('visitor', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->latest('check_in_time')->paginate(20);

        return view('admin.visitors.index', compact('visits'));
    }

    public function create()
    {
        $hosts = User::where('is_active', true)->orderBy('name')->get();
        $parkingZones = ParkingZone::where('is_active', true)
            ->where('available_slots', '>', 0)
            ->get();

        return view('admin.visitors.create', compact('hosts', 'parkingZones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255',
            'id_type' => 'required|in:national_id,passport,driving_license,other',
            'phone_number' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'host_id' => 'required|exists:users,id',
            'visit_purpose' => 'required|string|max:500',
            'visit_notes' => 'nullable|string',
            'expected_checkout_time' => 'nullable|date',
            'temperature' => 'nullable|numeric',
            // Vehicle fields
            'plate_number' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|in:car,motorcycle,truck,van,suv,bus',
            'vehicle_make' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_color' => 'nullable|string|max:255',
            'parking_zone_id' => 'nullable|exists:parking_zones,id',
            'parking_slot' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            // Find or create visitor
            $visitor = Visitor::firstOrCreate(
                ['id_number' => $validated['id_number']],
                [
                    'full_name' => $validated['full_name'],
                    'phone_number' => $validated['phone_number'],
                    'email' => $validated['email'] ?? null,
                    'id_type' => $validated['id_type'],
                    'company' => $validated['company'] ?? null,
                ]
            );

            // Handle photo upload
            if ($request->hasFile('photo')) {
                if ($visitor->photo_path) {
                    Storage::disk('public')->delete($visitor->photo_path);
                }
                $visitor->photo_path = $request->file('photo')->store('visitors', 'public');
                $visitor->save();
            }

            // Create visit
            $visit = VisitorVisit::create([
                'visitor_id' => $visitor->id,
                'host_id' => $validated['host_id'],
                'visit_purpose' => $validated['visit_purpose'],
                'visit_notes' => $validated['visit_notes'] ?? null,
                'check_in_time' => now(),
                'expected_checkout_time' => $validated['expected_checkout_time'] ?? null,
                'badge_number' => 'VIS-' . str_pad(VisitorVisit::count() + 1, 6, '0', STR_PAD_LEFT),
                'status' => 'active',
                'temperature' => $validated['temperature'] ?? null,
            ]);

            // Handle vehicle if provided
            if ($request->filled('plate_number')) {
                $vehicle = Vehicle::firstOrCreate(
                    ['plate_number' => strtoupper($validated['plate_number'])],
                    [
                        'vehicle_type' => $validated['vehicle_type'] ?? 'car',
                        'make' => $validated['vehicle_make'] ?? null,
                        'model' => $validated['vehicle_model'] ?? null,
                        'color' => $validated['vehicle_color'] ?? null,
                    ]
                );

                $visit->vehicleVisit()->create([
                    'vehicle_id' => $vehicle->id,
                    'parking_zone_id' => $validated['parking_zone_id'] ?? null,
                    'parking_slot' => $validated['parking_slot'] ?? null,
                    'entry_time' => now(),
                    'status' => 'parked',
                ]);

                // Update parking zone availability
                if (isset($validated['parking_zone_id'])) {
                    $zone = ParkingZone::find($validated['parking_zone_id']);
                    $zone->decrement('available_slots');
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.visitors.show', $visit->id)
                ->with('success', 'Visitor checked in successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to check in visitor: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $visit = VisitorVisit::with([
            'visitor',
            'host',
            'vehicleVisit.vehicle',
            'vehicleVisit.parkingZone',
            'assetCheckouts.asset'
        ])->findOrFail($id);

        return view('admin.visitors.show', compact('visit'));
    }

    public function edit(Visitor $visitor)
    {
        $hosts = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.visitors.edit', compact('visitor', 'hosts'));
    }

    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($visitor->photo_path) {
                Storage::disk('public')->delete($visitor->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('visitors', 'public');
        }

        $visitor->update($validated);

        return redirect()
            ->route('admin.visitors.show', $visitor->visits()->latest()->first()->id)
            ->with('success', 'Visitor updated successfully!');
    }

    public function destroy(Visitor $visitor)
    {
        if ($visitor->photo_path) {
            Storage::disk('public')->delete($visitor->photo_path);
        }

        $visitor->delete();

        return redirect()
            ->route('admin.visitors.index')
            ->with('success', 'Visitor deleted successfully!');
    }

    public function checkout(Visitor $visitor)
    {
        $activeVisit = $visitor->visits()->where('status', 'active')->first();

        if (!$activeVisit) {
            return back()->with('error', 'No active visit found for this visitor.');
        }

        DB::beginTransaction();

        try {
            // Update visit
            $activeVisit->update([
                'actual_checkout_time' => now(),
                'status' => 'completed',
            ]);

            // Handle vehicle checkout
            if ($activeVisit->vehicleVisit) {
                $activeVisit->vehicleVisit->update([
                    'exit_time' => now(),
                    'status' => 'exited',
                    'duration_minutes' => now()->diffInMinutes($activeVisit->vehicleVisit->entry_time),
                ]);

                // Update parking zone availability
                if ($activeVisit->vehicleVisit->parking_zone_id) {
                    ParkingZone::find($activeVisit->vehicleVisit->parking_zone_id)->increment('available_slots');
                }
            }

            DB::commit();

            return back()->with('success', 'Visitor checked out successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to checkout visitor: ' . $e->getMessage());
        }
    }

    public function checkin(Visitor $visitor)
    {
        // Implement re-check-in logic if needed
        return back()->with('info', 'Check-in functionality coming soon!');
    }

    public function blacklist(Visitor $visitor)
    {
        $visitor->update(['is_blacklisted' => true]);
        return back()->with('success', 'Visitor has been blacklisted.');
    }

    public function removeBlacklist(Visitor $visitor)
    {
        $visitor->update(['is_blacklisted' => false]);
        return back()->with('success', 'Visitor has been removed from blacklist.');
    }
}
