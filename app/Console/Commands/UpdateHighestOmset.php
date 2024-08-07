<?php

namespace App\Console\Commands;

use App\Models\Sales;
use App\Models\DataAntrian;
use Illuminate\Console\Command;

class UpdateHighestOmset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omset:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update omset tertinggi setiap bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //ambil transaksi yang terjadi pada bulan ini
        $antrian = DataAntrian::with('pembayaran')->whereMonth('created_at', date('m'))->get();

        //ambil sales yang terlibat dalam transaksi
        $sales = $antrian->pluck('sales_id')->unique();

        //looping sales
        foreach ($sales as $sales_id) {
            //ambil transaksi sales
            $sales_transaction = $antrian->where('sales_id', $sales_id);

            //hitung total omset sales
            $total_omset = $sales_transaction->pembayaran->sum('total_harga');

            //update omset tertinggi sales
            $sales = Sales::find($sales_id);
            if($sales->omset_tertinggi < $total_omset){
                $sales->omset_tertinggi = $total_omset;
                $sales->save();
            }
        }


    }
}
