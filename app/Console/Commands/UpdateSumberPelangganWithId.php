<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSumberPelangganWithId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-sumber-pelanggan-with-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the source of the customer with the ID.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating the source of the customer with the ID...');

        $customers = DB::table('customers')->where('infoPelanggan', 'RO WA')->get();

        foreach ($customers as $customer) {
            $sumberLama = $customer->infoPelanggan;
            $sumberBaru = DB::table('sumber_pelanggan')->where('nama_sumber', 'RO WhatsApp')->first();

            if ($sumberBaru) {
                DB::table('customers')->where('id', $customer->id)->update([
                    'infoPelanggan' => $sumberBaru->id,
                ]);

                $this->info('Customer with ID: '. $customer->id .' has been updated.');
            } else {
                $this->error('Customer with ID: '. $customer->id .' has no source.');
                continue;
            }
        }

        $this->info('Update completed successfully.');
    }
}
