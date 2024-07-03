<?php

namespace App\Http\Controllers\Api;

use App\Models\Sales;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\DataAntrian;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;
use App\Http\Controllers\Controller;

class StrukController extends Controller
{
    public function notaOrderJson($id)
    {
        $order = DataAntrian::with([
            'customer', 
            'sales', 
            'pembayaran' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'pengiriman'
        ])->where('ticket_order', $id)->first();

        $items = Barang::with(['job'])->where('ticket_order', $id)->get();
        $sales = $order->sales; // Already loaded with eager loading
        $infoBayar = $order->pembayaran->first(); // Since pembayaran is ordered by created_at desc

        $namaSales = $sales->sales_name;
        $alamatSales = $sales->address;
        $waSales = $sales->sales_phone;

        // HITUNG TOTAL HARGA
        $totalHarga = 0;
        $totalPacking = $order->pembayaran->biaya_packing ?? 0;
        $totalPasang = $infoBayar->biaya_pasang ?? 0;
        $diskon = $infoBayar->diskon ?? 0;

        foreach ($items as $item) {
            $totalHarga += $item->price * $item->qty;
        }

        $infoPengiriman = $order->pengiriman->first();
        $totalOngkir = $infoPengiriman->ongkir ?? 0;

        $grandTotal = $totalHarga + $totalPacking + $totalOngkir + $totalPasang - $diskon;
        $sisaTagihan = $grandTotal - $infoBayar->dibayarkan;

        return response()->json([
            'nama_toko' => $namaSales,
            'alamat_toko' => $alamatSales,
            'telepon' => $waSales,
            'order' => $order,
            'items' => $items,
            'sales' => $sales,
            'totalHarga' => $totalHarga,
            'totalPacking' => $totalPacking,
            'totalOngkir' => $totalOngkir,
            'totalPasang' => $totalPasang,
            'diskon' => $diskon,
            'grandTotal' => $grandTotal,
            'sisaTagihan' => $sisaTagihan,
            'infoBayar' => $infoBayar,
        ]);
    }
    
    public function retailJson(String $id)
    {
        $bulan = date('m');
        $awal = date('Y-04-01');
        $akhir = date('Y-04-t');

        $penjualan = Penjualan::with(['customer'])->where('sales_id', $id)->whereBetween('created_at', [$awal, $akhir])->take(30)->get();

        return response()->json($penjualan);
    }

    public function listSales()
    {
        $sales = Sales::all();
        return response()->json($sales);
    }
    
    public function salesInfo(String $id)
    {
        $sales = Sales::find($id);
        return response()->json($sales);
    }
    
    public function retailCetakById(string $id)
    {
        try {
            $penjualan = Penjualan::with(['customer', 'sales'])->find($id);
            $items = PenjualanDetail::with(['penjualan','produk'])->where('penjualan_id', $id)->get();

            return response()->json([
                'penjualan' => $penjualan,
                'items' => $items
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
