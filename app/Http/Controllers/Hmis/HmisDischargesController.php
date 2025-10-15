<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class HmisDischargesController extends Controller
{
    // ==================== DISCHARGES DONE ====================
    
    public function done(Request $request): View
    {
        return view('hmis.discharges.done');
    }

    public function fetch(Request $request): JsonResponse
    {
        try {
            $records = $this->fetchDoneRecords();

            return response()->json([
                'success' => true,
                'count' => count($records),
                'records' => $records,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching done discharges: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0,
                'records' => [],
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    public function poll(Request $request): JsonResponse
    {
        try {
            $lastCheck = $request->input('last_check');
            $records = $this->fetchDoneRecords();

            if ($lastCheck) {
                $records = array_filter($records, function($r) use ($lastCheck) {
                    return isset($r['ActualDischargeDate']) && $r['ActualDischargeDate'] > $lastCheck;
                });
                $records = array_values($records);
            }

            return response()->json([
                'success' => true,
                'count' => count($records),
                'records' => $records,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error polling done discharges: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0,
                'records' => [],
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    // ==================== DISCHARGE REQUESTS (PENDING) ====================
    
    public function requested(Request $request): View
    {
        return view('hmis.discharges.requested');
    }

    public function fetchRequested(Request $request): JsonResponse
    {
        try {
            $records = $this->fetchRequestedRecords();

            // Debug: Log the first record to see actual field names
            if (!empty($records)) {
                Log::info('First requested discharge record keys: ' . json_encode(array_keys($records[0])));
            }

            return response()->json([
                'success' => true,
                'count' => count($records),
                'records' => $records,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching requested discharges: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0,
                'records' => [],
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    public function pollRequested(Request $request): JsonResponse
    {
        try {
            $lastCheck = $request->input('last_check');
            $records = $this->fetchRequestedRecords();

            if ($lastCheck) {
                $records = array_filter($records, function($r) use ($lastCheck) {
                    return isset($r['DischargeDate']) && $r['DischargeDate'] > $lastCheck;
                });
                $records = array_values($records);
            }

            return response()->json([
                'success' => true,
                'count' => count($records),
                'records' => $records,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error polling requested discharges: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0,
                'records' => [],
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    public function clearCache(Request $request): JsonResponse
    {
        try {
            \Artisan::call('hmis:sync');
            
            return response()->json([
                'success' => true,
                'message' => 'Discharge cache cleared and re-synced successfully',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing discharge cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================
    
    private function fetchDoneRecords(): array
    {
        $results = DB::table('hmis_discharges_cache')
            ->orderByDesc('ActualDischargeDate')
            ->get();

        return $results->map(function($r) {
            return [
                'BranchID' => $r->BranchID ?? null,
                'PatientName' => $r->PatientName ?? 'N/A',
                'PatientNumber' => $r->PatientNumber ?? 'N/A',
                'NextOfKin' => $r->NextOfKin ?? 'N/A',
                'WardNumber' => $r->WardNumber ?? 'N/A',
                'BedNumber' => $r->BedNumber ?? 'N/A',
                'ActualDischargeDate' => $r->ActualDischargeDate ?? null,
                'DischargedBy' => $r->DischargedBy ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Fetch pending discharge requests - with case-insensitive property access
     */
    private function fetchRequestedRecords(): array
    {
        $results = DB::table('hmis_discharge_requests_cache')
            ->orderByDesc('DischargeDate')
            ->get();

        return $results->map(function($r) {
            return [
                'OccupancyID' => $r->OccupancyID ?? 'N/A',
                'PatientName' => $r->PatientName ?? 'N/A',
                'CustomerID' => $r->CustomerID ?? 'N/A',
                'WardNumber' => $r->WardNumber ?? 'N/A',
                'BedNumber' => $r->BedNumber ?? 'N/A',
                'AdmissionDate' => $r->AdmissionDate ? date('Y-m-d H:i', strtotime($r->AdmissionDate)) : 'N/A',
                'DischargeDate' => $r->DischargeDate ? date('Y-m-d H:i', strtotime($r->DischargeDate)) : 'N/A',
                'DischargingDoctorID' => $r->DischargingDoctorID ?? 'N/A',
            ];
        })->toArray();
    }


    private function formatDateTime($datetime): string
    {
        if (!$datetime) {
            return 'N/A';
        }

        try {
            $date = new \DateTime($datetime);
            return $date->format('Y-m-d H:i');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
