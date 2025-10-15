<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function clearCache(Request $request): JsonResponse
    {
        // Trigger immediate re-sync
        \Artisan::call('hmis:sync');
        
        return response()->json([
            'success' => true,
            'message' => 'Ward cache cleared and re-synced successfully',
        ]);
    }

    private function fetchAllRecords(): array
    {
        // Read from local cache table - INSTANT!
        $results = DB::table('hmis_ward_cache')
            ->orderByDesc('AdmissionDate')
            ->get();

        return $results->map(function($r) {
            return [
                'Branch' => $r->Branch,
                'PatientName' => $r->PatientName,
                'PatientNumber' => $r->PatientNumber,
                'NOKDetails' => $r->NOKDetails,
                'WardNumber' => $r->WardNumber,
                'BedNumber' => $r->BedNumber,
                'AdmissionDate' => $r->AdmissionDate,
            ];
        })->toArray();
    }
}
