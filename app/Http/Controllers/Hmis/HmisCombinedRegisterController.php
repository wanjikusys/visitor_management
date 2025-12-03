<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class HmisCombinedRegisterController extends Controller
{
    /**
     * Display the main combined register view.
     */
    public function index(Request $request): View
    {
        return view('hmis.combined_register');
    }

    /**
     * Fetch, combine, and standardize all patient records.
     */
    public function fetch(Request $request): JsonResponse
    {
        try {
            $records = $this->fetchAllRecords();

            return response()->json([
                'success' => true,
                'count' => count($records),
                'records' => $records,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch combined records: ' . $e->getMessage(),
                'records' => [],
            ], 500);
        }
    }

    /**
     * Clears the local cache for all underlying sources and triggers a re-sync.
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            // Calls the artisan command that runs HmisSyncService::syncAll()
            Artisan::call('hmis:sync');
            
            return response()->json([
                'success' => true,
                'message' => 'Combined register cache cleared and re-synced successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetches and standardizes records from OPD, Ward, Theatre and Daycase caches.
     */
    private function fetchAllRecords(): array
    {
        // 1. Fetch and standardize OPD records
        $opdRecords = [];
        if (Schema::hasTable('hmis_opd_cache')) {
            $opdRecords = DB::table('hmis_opd_cache')
                ->select('Branch', 'PatientName', 'PatientNumber', 'NextOfKin', 'DateTimeIn', 'Status')
                ->orderByDesc('DateTimeIn')
                ->get()
                ->map(function($r) {
                    return [
                        'Type' => 'OPD',
                        'Branch' => $r->Branch ?? 'N/A',
                        'PatientName' => $r->PatientName ?? 'N/A',
                        'PatientNumber' => $r->PatientNumber ?? 'N/A',
                        'NextOfKin' => $r->NextOfKin ?? 'N/A',
                        'Location' => 'Outpatient', 
                        'Details' => $r->Status ?? 'N/A',
                        'DateTimeIn' => $r->DateTimeIn ?? now()->toDateTimeString(),
                    ];
                })->toArray();
        }

        // 2. Fetch and standardize WARD records
        $wardRecords = [];
        if (Schema::hasTable('hmis_ward_cache')) {
            $wardRecords = DB::table('hmis_ward_cache')
                ->select('Branch', 'PatientName', 'PatientNumber', 'NOKDetails', 'WardNumber', 'BedNumber', 'AdmissionDate')
                ->orderByDesc('AdmissionDate')
                ->get()
                ->map(function($r) {
                    return [
                        'Type' => 'WARD',
                        'Branch' => $r->Branch ?? 'N/A',
                        'PatientName' => $r->PatientName ?? 'N/A',
                        'PatientNumber' => $r->PatientNumber ?? 'N/A',
                        'NextOfKin' => $r->NOKDetails ?? 'N/A',
                        'Location' => $r->WardNumber ?? 'N/A',
                        'Details' => $r->BedNumber ? "Bed: {$r->BedNumber}" : 'N/A',
                        'DateTimeIn' => $r->AdmissionDate ?? now()->toDateTimeString(),
                    ];
                })->toArray();
        }

        // 3. Fetch and standardize THEATRE records
        $theatreRecords = [];
        if (Schema::hasTable('hmis_theatre_cache')) {
            $theatreRecords = DB::table('hmis_theatre_cache')
                ->select('PatientName', 'PatientNumber', 'NOKName', 'OperationRoom', 'SessionDate', 'SessionType', 'Status')
                ->orderByDesc('SessionDate')
                ->get()
                ->map(function($r) {
                    return [
                        'Type' => 'THEATRE',
                        'Branch' => 'KIJABE', // Defaulting to Kijabe as it's implied in theatre sync
                        'PatientName' => $r->PatientName ?? 'N/A',
                        'PatientNumber' => $r->PatientNumber ?? 'N/A',
                        'NextOfKin' => $r->NOKName ?? 'N/A',
                        'Location' => $r->OperationRoom ?? 'Theatre',
                        'Details' => ($r->SessionType ?? 'Procedure') . ' (' . ($r->Status ?? 'Unknown') . ')',
                        'DateTimeIn' => $r->SessionDate ?? now()->toDateTimeString(),
                    ];
                })->toArray();
        }

        // 4. Combine all records
        $combined = array_merge($opdRecords, $wardRecords, $theatreRecords);

        // 5. Sort the combined array by the time they entered (most recent first)
        usort($combined, function($a, $b) {
            return strtotime($b['DateTimeIn']) <=> strtotime($a['DateTimeIn']);
        });

        return $combined;
    }
}