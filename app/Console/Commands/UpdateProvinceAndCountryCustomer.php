<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateProvinceAndCountryCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-province-and-country-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = Customer::where('alamat', '!=', null)->get();
        foreach ($customers as $customer) {
            $alamat = $customer->alamat;
            //Mengambil provinsi dari alamat
            $provinsi = $this->getProvince($alamat);
            //Mengambil kota dari alamat
            $kota = $this->getCity($alamat);
        }
    }

    private function getProvince($alamat)
    {
        $provinsi = Http::get('https://sipedas.pertanian.go.id/api/wilayah/list_pro?thn=2024')->json();
        foreach ($provinsi as $prov => $value) {
            if (Str::contains($alamat, $value)) {
                return $prov;
            }
        }
        return null;
    }
}
