<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;
use App\Models\DataAntrian;
use Illuminate\Http\Request;
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

        if (!$order) {
            return response()->json([
                'error' => 'Data order tidak ditemukan.'
            ], 404);
        }

        $items = Barang::with(['job'])->where('ticket_order', $id)->get();
        $sales = $order->sales; // Already loaded with eager loading
        $infoBayar = $order->pembayaran->first(); // Since pembayaran is ordered by created_at desc

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
            'message' => 'success'
        ]);
    }
}
