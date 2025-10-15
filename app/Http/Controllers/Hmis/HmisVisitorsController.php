<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use App\Models\InpatientVisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HmisVisitorsController extends Controller
{
    /**
     * Display inpatient visitors (ward register) page
     */
    public function index(Request $request): View
    {
        return view('hmis.visitors.index');
    }

    /**
     * Fetch all ward/IPD records with visitor logs
     */
    public function fetch(Request $request): JsonResponse
    {
        $records = $this->fetchAllRecords();

        return response()->json([
            'success' => true,
            'count' => count($records),
            'records' => $records,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Store visitor check-in
     */
    public function storeVisitor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_number' => 'required|string|max:50',
            'patient_name' => 'required|string|max:255',
            'ward_number' => 'required|string|max:50',
            'bed_number' => 'required|string|max:50',
            'visitor_name' => 'required|string|max:255',
            'visitor_id_number' => 'nullable|string|max:50',
            'visitor_phone' => 'nullable|string|max:20',
            'relationship' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $visitor = InpatientVisitorLog::create([
            ...$validated,
            'check_in_time' => now(),
            'checked_in_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked in successfully',
            'visitor' => $visitor,
        ]);
    }

    /**
     * Check out visitor
     */
    public function checkoutVisitor(Request $request, $id): JsonResponse
    {
        $visitor = InpatientVisitorLog::findOrFail($id);

        $visitor->update([
            'check_out_time' => now(),
            'checked_out_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked out successfully',
        ]);
    }

    /**
     * Get active visitors for a patient
     */
    public function getActiveVisitors(Request $request, $patientNumber): JsonResponse
    {
        $visitors = InpatientVisitorLog::where('patient_number', $patientNumber)
            ->whereNull('check_out_time')
            ->with(['checkedInBy', 'checkedOutBy'])
            ->orderByDesc('check_in_time')
            ->get();

        return response()->json([
            'success' => true,
            'visitors' => $visitors,
        ]);
    }

    /**
     * Poll for new records
     */
    public function poll(Request $request): JsonResponse
    {
        $lastCheck = $request->input('last_check');
        $records = $this->fetchAllRecords();

        if ($lastCheck) {
            $records = array_filter($records, function($r) use ($lastCheck) {
                return $r['AdmissionDate'] > $lastCheck;
            });
            $records = array_values($records);
        }

        return response()->json([
            'success' => true,
            'count' => count($records),
            'records' => $records,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache(Request $request): JsonResponse
    {
        \Artisan::call('hmis:sync');
        
        return response()->json([
            'success' => true,
            'message' => 'Ward cache cleared and re-synced successfully',
        ]);
    }

    /**
     * Fetch all ward records with active visitor counts
     */
    private function fetchAllRecords(): array
    {
        $wardRecords = DB::table('hmis_ward_cache')
            ->orderByDesc('AdmissionDate')
            ->get();

        // Get active visitor counts per patient
        $visitorCounts = DB::table('inpatient_visitor_logs')
            ->whereNull('check_out_time')
            ->select('patient_number', DB::raw('COUNT(*) as active_visitors'))
            ->groupBy('patient_number')
            ->pluck('active_visitors', 'patient_number');

        return $wardRecords->map(function($r) use ($visitorCounts) {
            return [
                'Branch' => $r->Branch,
                'PatientName' => $r->PatientName,
                'PatientNumber' => $r->PatientNumber,
                'NOKDetails' => $r->NOKDetails,
                'WardNumber' => $r->WardNumber,
                'BedNumber' => $r->BedNumber,
                'AdmissionDate' => $r->AdmissionDate,
                'ActiveVisitors' => $visitorCounts[$r->PatientNumber] ?? 0,
            ];
        })->toArray();
    }
}
