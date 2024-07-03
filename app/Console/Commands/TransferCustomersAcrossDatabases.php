<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferCustomersAcrossDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:transfer-across-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer customers from old database to new database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldCustomers = DB::connection('mysql_old')->table('customers')->orderBy('id')->chunk(100, function ($customers) {
            foreach ($oldCustomers as $customer) {
                DB::connection('mysql')->table('customers')->insert([
                    'id' => $customer->id,
                    'nama' => $customer->nama,
                    'telepon' => $customer->telepon,
                    'alamat' => $customer->alamat,
                    'infoPelanggan' => $customer->infoPelanggan,
                    'instansi' => $customer->instansi,
                    'frekuensi_order' => $customer->frekuensi_order,
                    'count_followUp' => 0,
                    'sales_id' => 1,
                    'provinsi' => null,
                    'kota' => null,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ]);
            }
        });

        $this->info('Customers transferred successfully!');
    }
}
