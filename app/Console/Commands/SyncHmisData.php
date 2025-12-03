<?php

namespace App\Console\Commands;

use App\Services\HmisSyncService;
use Illuminate\Console\Command;

class SyncHmisData extends Command
{
    protected $signature = 'hmis:sync 
                            {--force : Force sync even if already running}';
    
    protected $description = 'Sync HMIS data from remote SQL Server to local cache';

    public function handle(HmisSyncService $syncService): int
    {
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║   HMIS Data Sync - Starting...         ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        $startTime = microtime(true);

        try {
            // Show progress
            $this->info('📊 Syncing HMIS Data...');
            // NOTE: You must ensure HmisSyncService::syncAll() now includes 'daycase' records.
            $results = $syncService->syncAll();

            $this->newLine();
            $this->info('✅ Sync Results:');
            $this->table(
                ['Module', 'Records Synced'],
                [
                    ['OPD Register', $results['opd'] ?? 0],
                    ['Ward/IPD', $results['ward'] ?? 0],
                    ['Daycase Patients', $results['daycase'] ?? 0], // ADDED Daycase to the report
                    ['Discharges Done', $results['discharges'] ?? 0],
                    ['Discharge Requests', $results['discharge_requests'] ?? 0],
                ]
            );

            // Use array_sum and handle potential missing keys gracefully
            $total = array_sum(array_values($results)); 
            $duration = round(microtime(true) - $startTime, 2);
            
            $this->newLine();
            $this->info("🎉 Total: {$total} records synced in {$duration}s");
            $this->newLine();
            $this->info('╔════════════════════════════════════════╗');
            $this->info('║   Sync Completed Successfully! ✓       ║');
            $this->info('╚════════════════════════════════════════╝');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            // You should rely on Laravel's error logging, but this is a good CLI backup:
            $this->error('Trace: ' . $e->getFile() . ' on line ' . $e->getLine()); 
            return Command::FAILURE;
        }
    }
}