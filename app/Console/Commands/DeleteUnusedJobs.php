<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUnusedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-unused-jobs {--dry-run : Count unused jobs without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unused jobs from the jobs table.';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $unusedJobs = DB::table('jobs')
            ->whereNotIn('id', function ($query) {
                $query->select('job_id')
                    ->from('orders')
                    ->whereNotNull('job_id')
                    ->distinct();
            })
            ->pluck('id');
        if ($this->option('dry-run')) {
            $this->info(count($unusedJobs) . " unused job(s) would be deleted:");
            foreach ($unusedJobs as $jobId) {
                $this->line("- Job ID: " . $jobId);
            }
        } else {
            if ($this->confirm('Are you sure you want to delete ' . count($unusedJobs) . ' unused jobs?')) {
                $deletedCount = DB::table('jobs')->whereIn('id', $unusedJobs)->delete();
                $this->info("Deleted {$deletedCount} unused job(s).");
            } else {
                $this->info('Operation cancelled.');
            }
        }
    }
}
