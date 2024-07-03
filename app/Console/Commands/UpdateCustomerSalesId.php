<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCustomerSalesId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-customer-sales-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update customers sales_id from antrian table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating customer sales_id...');

        DB::transaction(function () {
            $affected = DB::table('customers')
                ->join('antrians', 'customers.id', '=', 'antrians.customer_id')
                ->where('customers.sales_id', '==', 0)
                ->update(['customers.sales_id' => DB::raw('antrians.sales_id')]);

            $this->info("Updated {$affected} customer records.");
        });
            
        $this->info('Update completed successfully.');
    }
}
