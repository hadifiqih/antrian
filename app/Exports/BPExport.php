<?php

namespace App\Exports;

use App\Models\Bahan;
use App\Models\Barang;
use App\Models\BiayaLain;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BPExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $barang = Barang::find($this->id);

        $bahan = Bahan::where('barang_id', $this->id)->get();
        $totalProduksi = 0;
        foreach($bahan as $b){
            $totalProduksi += $b->harga * $b->qty;
        }

        if($barang->kategori_id == 3){
            $biayaLainnya = BiayaLain::all();
        }else{
            $biayaLainnya = [];
        }

        return view('page.estimator.bp-excel', [
            'barang' => $barang,
            'bahan' => $bahan,
            'totalProduksi' => $totalProduksi,
            'biayaLainnya' => $biayaLainnya
        ]);
    }
}
