<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Added for table existence check
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HmisWardController extends Controller
{
    public function index(Request $request): View
    {
        return view('hmis.ward.index');
    }

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

    public function poll(Request $request): JsonResponse
    {
        $lastCheck = $request->input('last_check');
        $records = $this->fetchAllRecords();

        if ($lastCheck) {
            $records = array_filter($records, function($r) use ($lastCheck) {
                // Ensure AdmissionDate exists and compare
                return isset($r['AdmissionDate']) && $r['AdmissionDate'] > $lastCheck;
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

    public function clearCache(Request $request): JsonResponse
    {
        // Trigger immediate re-sync
        \Artisan::call('hmis:sync');
        
        return response()->json([
            'success' => true,
            'message' => 'Ward and Theatre cache cleared and re-synced successfully',
        ]);
    }

    private function fetchAllRecords(): array
    {
        $wardRecords = collect();
        $theatreRecords = collect();

        // 1. Fetch Ward Records
        if (Schema::hasTable('hmis_ward_cache')) {
            $wardRecords = DB::table('hmis_ward_cache')
                ->select('Branch', 'PatientName', 'PatientNumber', 'NOKDetails', 'WardNumber', 'BedNumber', 'AdmissionDate')
                ->get()
                ->map(function($r) {
                    return [
                        'Source'        => 'WARD',
                        'Branch'        => $r->Branch,
                        'PatientName'   => $r->PatientName,
                        'PatientNumber' => $r->PatientNumber,
                        'NOKDetails'    => $r->NOKDetails,
                        'WardNumber'    => $r->WardNumber,
                        'BedNumber'     => $r->BedNumber,
                        'AdmissionDate' => $r->AdmissionDate,
                    ];
                });
        }

        // 2. Fetch Theatre Records
        if (Schema::hasTable('hmis_theatre_cache')) {
            $theatreRecords = DB::table('hmis_theatre_cache')
                ->select('PatientName', 'PatientNumber', 'NOKName', 'OperationRoom', 'SessionDate', 'SessionType', 'Status')
                ->get()
                ->map(function($r) {
                    return [
                        'Source'        => 'THEATRE',
                        'Branch'        => 'KIJABE',
                        'PatientName'   => $r->PatientName,
                        'PatientNumber' => $r->PatientNumber,
                        'NOKDetails'    => $r->NOKName,
                        // Map "OperationRoom" to "WardNumber" column for display consistency
                        'WardNumber'    => $r->OperationRoom ?? 'Theatre', 
                        // Map "SessionType" (e.g., C-Section) to "BedNumber" column
                        'BedNumber'     => $r->SessionType ?? $r->Status, 
                        // Map "SessionDate" to "AdmissionDate" for sorting/polling
                        'AdmissionDate' => $r->SessionDate, 
                    ];
                });
        }

        // 3. Merge and Sort by Date (Most recent first)
        $merged = $wardRecords->merge($theatreRecords)
            ->sortByDesc('AdmissionDate')
            ->values()
            ->toArray();

        return $merged;
    }
}