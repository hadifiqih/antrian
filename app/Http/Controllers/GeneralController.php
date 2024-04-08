<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeneralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getProvinsi()
    {
        //Mengambil data provinsi dari API
        $provinsi = Http::get('https://sipedas.pertanian.go.id/api/wilayah/list_pro?thn=2023')->json();

        //Mengembalikan data provinsi dalam bentuk JSON
        return response()->json($provinsi);
    }

    public function getKota(Request $request)
    {
        //Mengambil data kota dari API
        $kota = Http::get('https://sipedas.pertanian.go.id/api/wilayah/list_kab?thn=2023&lvl=11&pro=' . $request->provinsi)->json();

        //Mengembalikan data kota dalam bentuk JSON
        return response()->json($kota);
    }

    public function getTotalOmsetBulanan(string $month)
    {
        if ($month != null) {
            $year = date('Y'); // Mengambil tahun saat ini
        
            $mulai = date('Y-' . $month . '-01 00:00:00');
            $akhirBulan = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $selesai = date('Y-' . $month . '-' . $akhirBulan . ' 23:59:59');
        } else {
            $mulai = date('Y-m-01 00:00:00');
            $selesai = date('Y-m-d 23:59:59');
        }

        $salesId = auth()->user()->id;

        $antrians = Barang::whereHas('antrian', function ($query) use ($mulai, $selesai, $salesId) {
            $query->whereBetween('created_at', [$mulai, $selesai]);
        })
        ->where('user_id', $salesId)
        ->get();

        $totalOmset = 0;
        foreach ($antrians as $antrian) {
            $totalOmset += $antrian->price * $antrian->qty;
        }

        return number_format($totalOmset,0,',','.');
    }
}
