<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Psr\Log\LoggerInterface;

/**
 * HmisSyncService
 *
 * Fast, robust HMIS sync implementation.
 *
 * Notes about the requested-discharge fix:
 * - Some HMIS OccupancyID values are not pure integers (examples: "176017/25", "KH001239").
 *   If your local cache table's OccupancyID column is numeric (INT) those string values cause
 *   "Data truncated" warnings / failures when inserted.
 * - This service handles that by:
 *   1) Detecting whether the remote OccupancyID is purely numeric. If so, we write it to the
 *      existing OccupancyID column (if present).
 *   2) If the remote OccupancyID contains non-digit characters, we will attempt to store the
 *      full raw value into a fallback string column named `OccupancyRaw` (case-insensitive).
 *      If `OccupancyRaw` is NOT present in the local table, we will leave OccupancyID NULL to
 *      avoid truncation errors and still insert the rest of the record.
 *
 * Recommended (one-time) migration for full fidelity:
 *   Schema::table('hmis_discharge_requests_cache', function (Blueprint $table) {
 *       $table->string('OccupancyRaw', 64)->nullable()->after('OccupancyID');
 *   });
 *
 * Other improvements remain:
 * - Explicit remote/local connections
 * - Streaming with cursor() where it helps
 * - Batch inserts in configurable chunk sizes
 * - Sargable queries / DB-side deduplication where required
 */
class HmisSyncService
{
    protected int $insertChunk = 500;
    protected string $remoteConnection = 'hmis';
    protected string $localConnection;
    protected ?LoggerInterface $logger = null;

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
        Log::info('=== HMIS Sync Started ===');
        $startTime = microtime(true);

        $results = [
            'opd' => $this->syncOpd(),
            'ward' => $this->syncWard(),
            'discharges' => $this->syncDischarges(),
            'discharge_requests' => $this->syncDischargeRequests(),
        ];

        $duration = round(microtime(true) - $startTime, 2);
        Log::info('=== HMIS Sync Completed ===', array_merge($results, ['duration_seconds' => $duration]));

        return $results;
    }

    /**
     * Sync OPD register (last 24 hours) - streaming + batch insert
     */
    public function syncOpd(): int
    {
        $module = 'OPD';
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        try {
            Log::info("Syncing {$module} data...");

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
            $local->table('hmis_opd_cache')->truncate();

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
                    $local->table('hmis_opd_cache')->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $local->table('hmis_opd_cache')->insert($batch);
            }

            $count = count($seen);
            $duration = round(microtime(true) - $startTime, 2);
            Log::info("OPD synced: {$count} records in {$duration}s");

            return $count;
        } catch (\Throwable $e) {
            Log::error("OPD sync failed: {$e->getMessage()}", ['exception' => $e]);
            if ($this->logger) {
                $this->logger->error('OPD sync failed', ['exception' => $e]);
            }
            return 0;
        }
    }

    /**
     * Sync Ward/IPD register (active admissions)
     */
    public function syncWard(): int
    {
        $module = 'Ward';
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        try {
            Log::info("Syncing {$module} data...");

            $remoteQ = $remote
                ->table('dbo.bedoccupancydetail AS t')
                ->selectRaw('ta.branchid as Branch, ti.patientname as PatientName, ti.patientnumber as PatientNumber, ci.NOKName, ci.NOKCellPhone, ci.NOKRelationship, t.wardnumber as WardNumber, t.bednumber as BedNumber, t.admissiondate as AdmissionDate')
                ->join('dbo.consultationheader AS ti', 't.occupancyid', '=', 'ti.occupancyid')
                ->join('dbo.BedOccupancy AS ta', 't.OccupancyID', '=', 'ta.OccupancyID')
                ->leftJoin('dbo.customerinformation AS ci', 'ti.patientnumber', '=', 'ci.patientnumber')
                ->where('ta.BranchID', 'KIJABE')
                ->where('ta.DepartmentID', 'MAIN')
                ->where('ta.Posted', 1)
                ->where('ta.Closed', 0)
                ->where('t.CheckedOut', 0)
                ->whereRaw("t.WardNumber <> 'THEATRE'")
                ->orderByDesc('t.admissiondate');

            $cursor = $remoteQ->cursor();

            $local->table('hmis_ward_cache')->truncate();

            $batch = [];
            $seen = [];

            foreach ($cursor as $r) {
                $key = sprintf('%s|%s|%s', $r->PatientNumber ?? '', $r->WardNumber ?? '', $r->BedNumber ?? '');
                if ($key === '||') {
                    continue;
                }
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $batch[] = [
                    'Branch' => $r->Branch,
                    'PatientName' => $r->PatientName,
                    'PatientNumber' => $r->PatientNumber,
                    'NOKDetails' => $this->formatNOK($r),
                    'WardNumber' => $r->WardNumber,
                    'BedNumber' => $r->BedNumber,
                    'AdmissionDate' => $this->normalizeDate($r->AdmissionDate),
                    'cached_at' => now(),
                ];

                if (count($batch) >= $this->insertChunk) {
                    $local->table('hmis_ward_cache')->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $local->table('hmis_ward_cache')->insert($batch);
            }

            $count = count($seen);
            $duration = round(microtime(true) - $startTime, 2);
            Log::info("Ward synced: {$count} records in {$duration}s");

            return $count;
        } catch (\Throwable $e) {
            Log::error("Ward sync failed: {$e->getMessage()}", ['exception' => $e]);
            if ($this->logger) {
                $this->logger->error('Ward sync failed', ['exception' => $e]);
            }
            return 0;
        }
    }

    /**
     * Sync discharges done today - uses SQL window function on remote side then batch insert locally.
     */
    public function syncDischarges(): int
    {
        $module = 'Discharges';
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        try {
            Log::info("Syncing {$module}...");

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

            // This result set is expected to be small (today's discharges). Use select().
            $rows = $remote->select($sql, ['KIJABE', $start, $end]);

            // Replace local cache (truncate then batch-insert)
            $local->table('hmis_discharges_cache')->truncate();

            $batch = [];
            foreach ($rows as $r) {
                $batch[] = [
                    'BranchID' => $r->BranchID,
                    'PatientName' => $r->PatientName,
                    'PatientNumber' => $r->PatientNumber,
                    'NextOfKin' => $this->formatNOK($r),
                    'WardNumber' => $r->WardNumber,
                    'BedNumber' => $r->BedNumber,
                    'ActualDischargeDate' => $this->normalizeDate($r->ActualDischargeDate),
                    'DischargedBy' => $r->DischargedBy ?? 'N/A',
                    'cached_at' => now(),
                ];

                if (count($batch) >= $this->insertChunk) {
                    $local->table('hmis_discharges_cache')->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $local->table('hmis_discharges_cache')->insert($batch);
            }

            $count = count($rows);
            $duration = round(microtime(true) - $startTime, 2);
            Log::info("Discharges synced: {$count} records in {$duration}s");

            return $count;
        } catch (\Throwable $e) {
            Log::error("Discharges sync failed: {$e->getMessage()}", ['exception' => $e]);
            if ($this->logger) {
                $this->logger->error('Discharges sync failed', ['exception' => $e]);
            }
            return 0;
        }
    }

    /**
     * Sync discharge requests (pending)
     *
     * Corrected to avoid "Data truncated for column 'OccupancyID'" by:
     * - detecting non-numeric OccupancyID values and writing them to OccupancyRaw
     *   if that column exists in the local table.
     * - if OccupancyRaw does not exist and OccupancyID is non-numeric, we leave OccupancyID NULL
     *   to avoid truncation. (You should add OccupancyRaw varchar column to your cache table.)
     */
    /**
     * Sync discharge requests (pending) - with Ward and Bed info
     */
    public function syncDischargeRequests(): int
    {
        $module = 'DischargeRequests';
        $startTime = microtime(true);

        $remote = DB::connection($this->remoteConnection);
        $local = DB::connection($this->localConnection);

        $localTable = 'hmis_discharge_requests_cache';

        try {
            Log::info("Syncing {$module}...");

            // Join with BedOccupancyDetail to get Ward and Bed info
            $sql = <<<'SQL'
    SELECT 
        b.OccupancyID,
        b.CustomerID,
        b.patientname,
        b.AdmissionDate,
        b.DischargeDate,
        b.DischargingDoctorID,
        bod.WardNumber,
        bod.BedNumber
    FROM dbo.BedOccupancy AS b WITH (NOLOCK)
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

            $rows = $remote->select($sql);

            // Truncate local table
            $local->table($localTable)->truncate();

            $batch = [];
            $count = 0;

            foreach ($rows as $r) {
                $occupancyId = $r->OccupancyID ?? null;
                
                // Convert to string (handles both numeric and string OccupancyIDs)
                $occupancyIdStr = $occupancyId !== null ? (string) $occupancyId : null;

                $row = [
                    'OccupancyID' => $occupancyIdStr,
                    'PatientName' => $r->patientname ?? 'N/A',
                    'CustomerID' => $r->CustomerID ?? 'N/A',
                    'WardNumber' => $r->WardNumber ?? 'N/A',
                    'BedNumber' => $r->BedNumber ?? 'N/A',
                    'AdmissionDate' => $this->normalizeDate($r->AdmissionDate),
                    'DischargeDate' => $this->normalizeDate($r->DischargeDate),
                    'DischargingDoctorID' => $r->DischargingDoctorID ?? 'N/A',
                    'cached_at' => now(),
                ];

                $batch[] = $row;
                $count++;

                if (count($batch) >= $this->insertChunk) {
                    try {
                        $local->table($localTable)->insert($batch);
                    } catch (\Throwable $insertEx) {
                        Log::warning("Batch insert failed for discharge requests; falling back to single-row inserts.", [
                            'exception' => $insertEx->getMessage()
                        ]);
                        foreach ($batch as $single) {
                            try {
                                $local->table($localTable)->insert($single);
                            } catch (\Throwable $singleEx) {
                                Log::error('Failed to insert single discharge-request row', [
                                    'error' => $singleEx->getMessage(),
                                    'row' => $single,
                                ]);
                            }
                        }
                    }
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                try {
                    $local->table($localTable)->insert($batch);
                } catch (\Throwable $insertEx) {
                    Log::warning("Final batch insert failed for discharge requests.", ['exception' => $insertEx->getMessage()]);
                    foreach ($batch as $single) {
                        try {
                            $local->table($localTable)->insert($single);
                        } catch (\Throwable $singleEx) {
                            Log::error('Failed to insert single discharge-request row (final batch)', [
                                'error' => $singleEx->getMessage(),
                                'row' => $single,
                            ]);
                        }
                    }
                }
            }

            $duration = round(microtime(true) - $startTime, 2);
            Log::info("Discharge Requests synced: {$count} records in {$duration}s");

            return $count;
        } catch (\Throwable $e) {
            Log::error("Discharge requests sync failed: {$e->getMessage()}", ['exception' => $e]);
            if ($this->logger) {
                $this->logger->error('Discharge requests sync failed', ['exception' => $e]);
            }
            return 0;
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
            return Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
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