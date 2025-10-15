<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use App\Models\Carer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HmisCarersController extends Controller
{
    public function index(Request $request): View
    {
        return view('hmis.carers.index');
    }

    /**
     * Get admitted patients (babies) for carer registration
     */
    public function getAdmittedPatients(Request $request): JsonResponse
    {
        $patients = DB::table('hmis_ward_cache')
            ->select('PatientNumber', 'PatientName', 'WardNumber', 'BedNumber')
            ->orderBy('PatientName')
            ->get();

        return response()->json([
            'success' => true,
            'patients' => $patients,
        ]);
    }

    /**
     * Store new carer registration
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_number' => 'required|string',
            'patient_name' => 'required|string',
            'ward' => 'required|string',
            'bed_number' => 'required|string',
            'carer_name' => 'required|string|max:255',
            'carer_contact' => 'required|string|max:20',
            'carer_id_number' => 'nullable|string|max:50',
            'relationship' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $carer = Carer::create([
            ...$validated,
            'baby_admitted' => $validated['patient_number'],
            'date_in' => now(),
            'registered_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Carer registered successfully',
            'carer' => $carer,
        ]);
    }

    /**
     * Get all active carers
     */
    public function getActiveCarers(Request $request): JsonResponse
    {
        $carers = Carer::whereNull('date_out')
            ->with('registeredBy')
            ->orderByDesc('date_in')
            ->get();

        return response()->json([
            'success' => true,
            'carers' => $carers,
        ]);
    }

    /**
     * Check out carer
     */
    public function checkout(Request $request, $id): JsonResponse
    {
        $carer = Carer::findOrFail($id);

        $carer->update([
            'date_out' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Carer checked out successfully',
        ]);
    }
}
