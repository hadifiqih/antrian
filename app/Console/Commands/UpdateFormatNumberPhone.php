<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateFormatNumberPhone extends Command
{
    protected $signature = 'update-format-number-phone';

    protected $description = 'Change the format of the phone number in the customer table.';

    public function handle()
    {
        $this->info('Updating the phone number format...');

        DB::transaction(function () {
            $customers = DB::table('customers')->get();

            foreach ($customers as $customer) {
                $phone = $customer->telepon;
                $phone = preg_replace('/[^0-9]/', '', $phone);

                if (strlen($phone) == 11 && substr($phone, 0, 1) == '0') {
                    $phone = '62' . substr($phone, 1);
                }

                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['telepon' => $phone]);

                $this->info('Updated phone number for customer ID: ' . $customer->id);
            }

            $this->info('All phone numbers have been updated.');
        });
            
        $this->info('Update completed successfully.');
    }
}
