<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HmisReportsController extends Controller
{
    public function index()
    {
        return view('hmis.reports.index');
    }

    public function visitorsReport(Request $request)
    {
        // Query from local inpatient_visitors table (adjust table name if needed)
        $query = DB::table('inpatient_visitors as iv')
            ->select(
                'iv.id',
                'iv.patient_number',
                'iv.patient_name',
                'iv.visitor_name',
                'iv.visitor_contact',
                'iv.relationship',
                'iv.check_in_time',
                'iv.check_out_time',
                'iv.purpose_of_visit',
                DB::raw('TIMESTAMPDIFF(MINUTE, iv.check_in_time, IFNULL(iv.check_out_time, NOW())) as duration_minutes')
            );

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->where('iv.check_in_time', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('iv.check_in_time', '<=', $request->end_date . ' 23:59:59');
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('iv.check_out_time');
            } else {
                $query->whereNotNull('iv.check_out_time');
            }
        }

        $visitors = $query->orderBy('iv.check_in_time', 'desc')->get();

        // Stats
        $stats = [
            'total' => $visitors->count(),
            'active' => $visitors->where('check_out_time', null)->count(),
            'completed' => $visitors->whereNotNull('check_out_time')->count(),
            'avg_duration' => round($visitors->whereNotNull('check_out_time')->avg('duration_minutes') ?? 0),
        ];

        if ($request->input('download') === 'excel') {
            return $this->downloadVisitorsExcel($visitors, $request);
        }

        return view('hmis.reports.visitors', compact('visitors', 'stats'));
    }

    public function vehiclesReport(Request $request)
    {
        $query = DB::table('hmis_vehicles as v')
            ->leftJoin('users as u1', 'v.checked_in_by', '=', 'u1.id')
            ->leftJoin('users as u2', 'v.checked_out_by', '=', 'u2.id')
            ->select(
                'v.*',
                'u1.name as checked_in_by_name',
                'u2.name as checked_out_by_name',
                DB::raw('TIMESTAMPDIFF(MINUTE, v.time_in, IFNULL(v.time_out, NOW())) as duration_minutes')
            );

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->where('v.time_in', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('v.time_in', '<=', $request->end_date . ' 23:59:59');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('v.checked_in', $request->status === 'active' ? 1 : 0);
        }

        $vehicles = $query->orderBy('v.time_in', 'desc')->get();

        // Stats
        $stats = [
            'total' => $vehicles->count(),
            'active' => $vehicles->where('checked_in', 1)->count(),
            'completed' => $vehicles->where('checked_in', 0)->count(),
            'total_passengers' => $vehicles->sum('passengers'),
            'avg_duration' => round($vehicles->where('checked_in', 0)->avg('duration_minutes') ?? 0),
        ];

        if ($request->input('download') === 'excel') {
            return $this->downloadVehiclesExcel($vehicles, $request);
        }

        return view('hmis.reports.vehicles', compact('vehicles', 'stats'));
    }

    private function downloadVisitorsExcel($visitors, $request)
    {
        $filename = 'inpatient-visitors-report-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($visitors) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Patient Number', 'Patient Name', 'Visitor Name', 'Contact', 'Relationship', 'Check-In', 'Check-Out', 'Duration (mins)', 'Purpose']);

            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->patient_number,
                    $visitor->patient_name,
                    $visitor->visitor_name,
                    $visitor->visitor_contact,
                    $visitor->relationship,
                    $visitor->check_in_time,
                    $visitor->check_out_time ?? 'Active',
                    $visitor->duration_minutes,
                    $visitor->purpose_of_visit,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function downloadVehiclesExcel($vehicles, $request)
    {
        $filename = 'vehicles-report-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($vehicles) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Card No', 'Driver Name', 'Registration', 'Phone', 'Purpose', 'Passengers', 'Time In', 'Time Out', 'Duration (mins)', 'Checked In By']);

            foreach ($vehicles as $vehicle) {
                fputcsv($file, [
                    $vehicle->card_no,
                    $vehicle->driver_name,
                    $vehicle->registration,
                    $vehicle->phone_number,
                    $vehicle->visit_purpose,
                    $vehicle->passengers,
                    $vehicle->time_in,
                    $vehicle->time_out ?? 'Active',
                    $vehicle->duration_minutes,
                    $vehicle->checked_in_by_name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
