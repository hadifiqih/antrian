<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateJobs extends Command
{
    protected $signature = 'jobs:delete-duplicates {--dry-run : Show duplicates without deleting}';
    protected $description = 'Delete duplicate jobs while keeping one entry for each job_id used in orders';

    public function handle()
    {
        $duplicates = DB::table('jobs')
            ->select('id')
            ->whereIn('id', function ($query) {
                $query->select('job_id')
                    ->from('orders')
                    ->whereNotNull('job_id')
                    ->distinct();
            })
            ->groupBy('id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('id');

        if ($this->option('dry-run')) {
            $this->info(count($duplicates) . " job_id(s) have duplicates:");
            foreach ($duplicates as $jobId) {
                $count = DB::table('jobs')->where('job_id', $jobId)->count();
                $this->line("- Job ID: {$jobId} ({$count} entries)");
            }
        } else {
            if ($this->confirm('Are you sure you want to delete duplicates for ' . count($duplicates) . ' job_id(s)?')) {
                $totalDeleted = 0;
                foreach ($duplicates as $jobId) {
                    $duplicateIds = DB::table('jobs')
                        ->where('job_id', $jobId)
                        ->orderBy('id')
                        ->pluck('id')
                        ->skip(1); // Keep the first entry

                    $deleted = DB::table('jobs')
                        ->whereIn('id', $duplicateIds)
                        ->delete();

                    $totalDeleted += $deleted;
                }
                $this->info("Deleted {$totalDeleted} duplicate job entries.");
            } else {
                $this->info('Operation cancelled.');
            }
        }
    }
}
