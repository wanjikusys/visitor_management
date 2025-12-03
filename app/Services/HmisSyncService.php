<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * HmisSyncService
 *
 * Fast, robust HMIS sync implementation.
 *
 * Updates:
 * - Refactored repetitive sync logic into a generic helper.
 * - Optimized Discharge Requests sync to include the Discharging Doctor's Name.
 * - Standardized logging using the injected LoggerInterface.
 */
class HmisSyncService
{
    protected int $insertChunk = 500;
    protected string $remoteConnection = 'hmis';
    protected string $localConnection;
    protected ?LoggerInterface $logger = null;

    /**
     * Map of module name to its local cache table name.
     */
    protected array $localTables = [
        'opd' => 'hmis_opd_cache',
        'ward' => 'hmis_ward_cache',
        'discharges' => 'hmis_discharges_cache',
        'discharge_requests' => 'hmis_discharge_requests_cache',
        'theatre_requests' => 'hmis_theatre_cache',
    ];

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        // Use the application default connection as local cache DB
        $this->localConnection = config('database.default') ?: 'mysql';
    }

    /**
     * Sync all HMIS data tables
     */
    public function syncAll(): array
    {
        $this->log('info', '=== HMIS Sync Started ===');
        $startTime = microtime(true);

        $results = [
            'opd' => $this->syncOpd(),
            'ward' => $this->syncWard(),
            'discharges' => $this->syncDischarges(),
            'discharge_requests' => $this->syncDischargeRequests(),
            'theatre_requests' => $this->syncTheatreRequests(),
        ];

        $duration = round(microtime(true) - $startTime, 2);
        $this->log('info', '=== HMIS Sync Completed ===', array_merge($results, ['duration_seconds' => $duration]));

        return $results;
    }

    /**
     * Sync OPD register (last 24 hours) - streaming + batch insert (unique logic, not using generic helper)
     */
    public function syncOpd(): int
    {
        $module = 'OPD';
        $localTable = $this->localTables['opd'];
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        try {
            $this->log('info', "Syncing {$module} data...");

            $since = Carbon::now()->subHours(24)->format('Y-m-d H:i:s');

            // Build remote query with sargable where clause
            $remoteQ = $remote
                ->table('dbo.consultationheader AS ch')
                ->selectRaw('ch.BranchId, ch.PatientName, ch.PatientNumber, ci.NOKName, ci.NOKCellPhone, ch.DateTimeIn, ch.PatientStatus')
                ->leftJoin('dbo.CustomerInformation AS ci', 'ch.PatientNumber', '=', 'ci.PatientNumber')
                ->whereRaw('ch.DateTimeIn >= ?', [$since])
                ->where('ch.BranchId', 'KIJABE')
                ->orderByDesc('ch.DateTimeIn');

            // Use cursor to stream
            $cursor = $remoteQ->cursor();

            // Clear local cache (no long transaction) then insert in batches
            $local->table($localTable)->truncate();

            $batch = [];
            $seen = [];

            foreach ($cursor as $row) {
                $patientNumber = (string) ($row->PatientNumber ?? '');
                if ($patientNumber === '') {
                    continue;
                }
                // dedupe: cursor ordered desc, first occurrence is latest
                if (isset($seen[$patientNumber])) {
                    continue;
                }
                $seen[$patientNumber] = true;

                $batch[] = [
                    'Branch' => $row->BranchId,
                    'PatientName' => $row->PatientName,
                    'PatientNumber' => $patientNumber,
                    'NextOfKin' => $this->formatNextOfKin($row),
                    'DateTimeIn' => $this->normalizeDate($row->DateTimeIn),
                    'Status' => $row->PatientStatus ?? null,
                    'cached_at' => now(),
                ];

                if (count($batch) >= $this->insertChunk) {
                    $local->table($localTable)->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $local->table($localTable)->insert($batch);
            }

            $count = count($seen);
            $duration = round(microtime(true) - $startTime, 2);
            $this->log('info', "OPD synced: {$count} records in {$duration}s");

            return $count;
        } catch (Throwable $e) {
            $this->log('error', "OPD sync failed: {$e->getMessage()}", ['exception' => $e]);
            return 0;
        }
    }

    /**
     * Sync Ward/IPD register (active admissions)
     * OPTIMIZED: Uses Raw SQL with NOLOCK to prevent delays.
     */
    public function syncWard(): int
    {
        $module = 'Ward';
        $localTable = $this->localTables['ward'];

        $sql = <<<'SQL'
SELECT 
    ta.BranchID AS Branch, 
    ti.PatientName, 
    ti.PatientNumber, 
    ci.NOKName, 
    ci.NOKCellPhone, 
    ci.NOKRelationship, 
    t.WardNumber, 
    t.BedNumber, 
    t.AdmissionDate
FROM dbo.BedOccupancyDetail AS t WITH (NOLOCK)
INNER JOIN dbo.consultationheader AS ti WITH (NOLOCK) 
    ON t.OccupancyID = ti.OccupancyID
INNER JOIN dbo.BedOccupancy AS ta WITH (NOLOCK) 
    ON t.OccupancyID = ta.OccupancyID
LEFT JOIN dbo.customerinformation AS ci WITH (NOLOCK) 
    ON ti.PatientNumber = ci.PatientNumber
WHERE 
    ta.BranchID = 'KIJABE'
    AND ta.DepartmentID = 'MAIN'
    AND ta.Posted = 1
    AND ta.Closed = 0
    AND t.CheckedOut = 0
    AND t.WardNumber <> 'THEATRE'
ORDER BY t.AdmissionDate DESC
SQL;
        $rowMapper = function ($r) {
            $key = sprintf('%s|%s|%s', $r->PatientNumber ?? '', $r->WardNumber ?? '', $r->BedNumber ?? '');

            return [
                'key' => $key, // Unique key for internal deduplication
                'data' => [
                    'Branch' => $r->Branch,
                    'PatientName' => $r->PatientName,
                    'PatientNumber' => $r->PatientNumber,
                    'NOKDetails' => $this->formatNOK($r),
                    'WardNumber' => $r->WardNumber,
                    'BedNumber' => $r->BedNumber,
                    'AdmissionDate' => $this->normalizeDate($r->AdmissionDate),
                    'cached_at' => now(),
                ]
            ];
        };

        return $this->executeSyncQueryAndCache($module, $localTable, $sql, [], $rowMapper);
    }

    /**
     * Sync Theatre Requests
     * Based on TheatreRequestHeader
     */
    public function syncTheatreRequests(): int
    {
        $module = 'Theatre';
        $localTable = $this->localTables['theatre_requests'];

        // Define date range: Yesterday to Tomorrow (covers active list + preparation)
        $startDate = Carbon::today()->subDays(1)->format('Y-m-d');
        $endDate = Carbon::today()->addDays(2)->format('Y-m-d');

        $sql = <<<'SQL'
SELECT DISTINCT
    a.PatientNumber,
    a.PatientName,
    PT.Gender,
    PT.NOKName,
    a.SessionDate,
    a.OperationRoom,
    a.SessionType,
    pe1.EmployeeName AS Consultant,
    a.Status,
    a.TheatreDayCase
FROM TheatreRequestHeader a WITH (NOLOCK)
LEFT JOIN CustomerInformation PT WITH (NOLOCK)
    ON a.PatientNumber = PT.CustomerID
LEFT JOIN PayrollEmployees pe1 WITH (NOLOCK)
    ON pe1.EmployeeID = a.SurgeonID AND pe1.BRANCHID = 'KIJABE'
WHERE 	
    a.SessionDate >= ? 
    AND a.SessionDate < ?
    AND a.Status <> 'Booking'
ORDER BY a.SessionDate DESC
SQL;

        $bindings = [$startDate, $endDate];

        $rowMapper = function ($r) {
            return [
                'data' => [
                    'PatientNumber' => $r->PatientNumber,
                    'PatientName' => $r->PatientName,
                    'Gender' => $r->Gender ?? null,
                    'NOKName' => $r->NOKName ?? null,
                    'SessionDate' => $this->normalizeDate($r->SessionDate),
                    'OperationRoom' => $r->OperationRoom ?? null,
                    'SessionType' => $r->SessionType ?? null,
                    'Consultant' => $r->Consultant ?? null,
                    'Status' => $r->Status ?? null,
                    'IsDayCase' => $r->TheatreDayCase ?? 0,
                    'cached_at' => now(),
                ]
            ];
        };

        return $this->executeSyncQueryAndCache($module, $localTable, $sql, $bindings, $rowMapper, true);
    }

    /**
     * Sync discharges done today - uses SQL window function on remote side then batch insert locally.
     */
    public function syncDischarges(): int
    {
        $module = 'Discharges';
        $localTable = $this->localTables['discharges'];

        $start = Carbon::today()->format('Y-m-d 00:00:00');
        $end = Carbon::tomorrow()->format('Y-m-d 00:00:00');

        $sql = <<<'SQL'
WITH ranked AS (
    SELECT
        t.BranchID,
        ti.patientname AS PatientName,
        ti.patientnumber AS PatientNumber,
        ci.NOKName,
        ci.NOKCellPhone,
        ci.NOKRelationship,
        t.WardNumber,
        t.BedNumber,
        t.ActualDischargeDate,
        t.checkedoutby AS DischargedBy,
        ROW_NUMBER() OVER (PARTITION BY ti.patientnumber ORDER BY t.ActualDischargeDate DESC) AS rn
    FROM dbo.BedOccupancyDetail AS t WITH (NOLOCK)
    JOIN dbo.consultationheader AS ti WITH (NOLOCK)
        ON t.occupancyid = ti.occupancyid
    LEFT JOIN dbo.customerinformation AS ci WITH (NOLOCK)
        ON ti.patientnumber = ci.patientnumber
    WHERE
        t.BranchID = ?
        AND t.CheckedOut = 1
        AND t.ActualDischargeDate >= ?
        AND t.ActualDischargeDate < ?
)
SELECT
    BranchID,
    PatientName,
    PatientNumber,
    NOKName,
    NOKCellPhone,
    NOKRelationship,
    WardNumber,
    BedNumber,
    ActualDischargeDate,
    DischargedBy
FROM ranked
WHERE rn = 1
ORDER BY ActualDischargeDate DESC
SQL;

        $bindings = ['KIJABE', $start, $end];

        $rowMapper = function ($r) {
            return [
                'data' => [
                    'BranchID' => $r->BranchID,
                    'PatientName' => $r->PatientName,
                    'PatientNumber' => $r->PatientNumber,
                    'NextOfKin' => $this->formatNOK($r),
                    'WardNumber' => $r->WardNumber,
                    'BedNumber' => $r->BedNumber,
                    'ActualDischargeDate' => $this->normalizeDate($r->ActualDischargeDate),
                    'DischargedBy' => $r->DischargedBy ?? 'N/A',
                    'cached_at' => now(),
                ]
            ];
        };

        return $this->executeSyncQueryAndCache($module, $localTable, $sql, $bindings, $rowMapper);
    }

    /**
     * Sync outstanding Discharge Requests, including the Discharging Doctor's Name.
     */
    public function syncDischargeRequests(): int
    {
        $module = 'DischargeRequests';
        $localTable = $this->localTables['discharge_requests'];

        // Added join to PayrollEmployees to fetch the Doctor's name
        $sql = <<<'SQL'
    SELECT 
        b.OccupancyID,
        b.CustomerID,
        b.patientname,
        b.AdmissionDate,
        b.DischargeDate,
        b.DischargingDoctorID,
        pe.EmployeeName AS DischargingDoctorName,
        bod.WardNumber,
        bod.BedNumber
    FROM dbo.BedOccupancy AS b WITH (NOLOCK)
    -- Join to get the doctor's name
    LEFT JOIN dbo.PayrollEmployees AS pe WITH (NOLOCK)
        ON pe.EmployeeID = b.DischargingDoctorID
    LEFT JOIN (
        SELECT DISTINCT 
            OccupancyID, 
            WardNumber, 
            BedNumber
        FROM dbo.BedOccupancyDetail WITH (NOLOCK)
        WHERE CheckedOut = 0
    ) AS bod ON b.OccupancyID = bod.OccupancyID
    WHERE b.DischargeRequested = 1
        AND b.Closed = 0
    ORDER BY b.DischargeDate DESC
    SQL;

        $rowMapper = function ($r) {
            $occupancyId = $r->OccupancyID !== null ? (string) $r->OccupancyID : null;

            return [
                'data' => [
                    'OccupancyID' => $occupancyId,
                    'PatientName' => $r->patientname ?? 'N/A',
                    'CustomerID' => $r->CustomerID ?? 'N/A',
                    'WardNumber' => $r->WardNumber ?? 'N/A',
                    'BedNumber' => $r->BedNumber ?? 'N/A',
                    'AdmissionDate' => $this->normalizeDate($r->AdmissionDate),
                    'DischargeDate' => $this->normalizeDate($r->DischargeDate),
                    'DischargingDoctorID' => $r->DischargingDoctorID ?? 'N/A',
                    // New field
                    'DischargingDoctorName' => $r->DischargingDoctorName ?? 'N/A',
                    'cached_at' => now(),
                ]
            ];
        };

        return $this->executeSyncQueryAndCache($module, $localTable, $sql, [], $rowMapper);
    }

    /**
     * Executes a raw SQL query against the remote HMIS, maps the results,
     * truncates the local table, and inserts the data in batches.
     *
     * @param string $module The name of the module being synced (for logging)
     * @param string $localTable The local database table name
     * @param string $sql The raw SQL query to execute on the remote connection
     * @param array $bindings Bindings for the SQL query
     * @param callable $rowMapper Function to map a remote row object to a local insert array. Must return ['data' => [...], 'key' => (string|null)]
     * @param bool $checkTableExistence Whether to check if the local table exists before proceeding.
     * @return int The number of records inserted
     */
    private function executeSyncQueryAndCache(
        string $module,
        string $localTable,
        string $sql,
        array $bindings,
        callable $rowMapper,
        bool $checkTableExistence = false
    ): int {
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        try {
            $this->log('info', "Syncing {$module} data...");

            if ($checkTableExistence && !Schema::connection($this->localConnection)->hasTable($localTable)) {
                $this->log('warning', "Table '{$localTable}' does not exist locally. Skipping sync for {$module}.");
                return 0;
            }

            $rows = $remote->select($sql, $bindings);

            $local->table($localTable)->truncate();

            $batch = [];
            $seen = [];
            $count = 0;

            foreach ($rows as $r) {
                $mapped = $rowMapper($r);
                $data = $mapped['data'];
                $key = $mapped['key'] ?? null;

                if ($key && isset($seen[$key])) {
                    continue;
                }
                if ($key) {
                    $seen[$key] = true;
                }

                $batch[] = $data;
                $count++;

                if (count($batch) >= $this->insertChunk) {
                    $local->table($localTable)->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $local->table($localTable)->insert($batch);
            }

            $duration = round(microtime(true) - $startTime, 2);
            $this->log('info', "{$module} synced: {$count} records in {$duration}s");

            return $count;

        } catch (Throwable $e) {
            $this->log('error', "{$module} sync failed: {$e->getMessage()}", ['exception' => $e]);
            return 0;
        }
    }


    /**
     * Standardized logging method.
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // Use injected logger if available, otherwise fallback to Laravel Log facade
        if ($this->logger) {
            $this->logger->{$level}($message, $context);
        } else {
            Log::{$level}($message, $context);
        }
    }

    /**
     * Normalize datetime values to Y-m-d H:i:s string (returns null on failure)
     */
    protected function normalizeDate($value): ?string
    {
        try {
            if ($value === null) {
                return null;
            }
            if ($value instanceof \DateTime) {
                return Carbon::instance($value)->format('Y-m-d H:i:s');
            }
            // Safely parse string/other value
            return Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Format Next of Kin for OPD (simplified)
     */
    private function formatNextOfKin($record): string
    {
        $name = $record->NOKName ?? null;
        $phone = $record->NOKCellPhone ?? null;

        if ($name && $phone) {
            return "{$name} ({$phone})";
        }

        return $name ?: ($phone ?: 'N/A');
    }

    /**
     * Format NOK with relationship (full format)
     */
    private function formatNOK($record): string
    {
        $name = $record->NOKName ?? null;
        $phone = $record->NOKCellPhone ?? null;
        $rel = $record->NOKRelationship ?? null;

        if ($name && $phone && $rel) {
            return "{$name} ({$phone}, {$rel})";
        }
        if ($name && $phone) {
            return "{$name} ({$phone})";
        }
        return $name ?: ($phone ?: 'N/A');
    }
}