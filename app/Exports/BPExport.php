<?php

namespace App\Exports;

use App\Models\Bahan;
use App\Models\Barang;
use App\Models\BiayaLain;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;

class BPExport implements FromView, WithColumnWidths, WithEvents
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $dataBahan = Bahan::where('barang_id', $this->id)->get();
                $dataBiayaLain = BiayaLain::all();

                $startRowBahan = 9;
                $totalRowBahan = count($dataBahan) + $startRowBahan;//11

                $startHeaderBiayaLain = $totalRowBahan + 2;//13
                $totalHeaderBiayaLain = $startHeaderBiayaLain + 1;//14

                $startRowBiayaLain = $totalRowBahan + 4;//15
                $akhirRowBiayaLain = $startRowBiayaLain + 8;//23

                $totalRowBiayaLain = $akhirRowBiayaLain + 1;//24

                $localeCurrencyMask = '[$Rp-421]#,##0';

                $sheet->mergeCells('D4:F4');
                $sheet->mergeCells('D6:F6');

                //SET CURRENCY
                $sheet->getStyle('D4')->getNumberFormat()->setFormatCode($localeCurrencyMask);
                $sheet->getStyle('D6')->getNumberFormat()->setFormatCode($localeCurrencyMask);
                foreach($dataBahan as $key => $bahan){
                    $sheet->getStyle('E'.$startRowBahan)->getNumberFormat()->setFormatCode($localeCurrencyMask);
                    $sheet->getStyle('F'.$startRowBahan)->getNumberFormat()->setFormatCode($localeCurrencyMask);
                    $startRowBahan++;
                }
                foreach($dataBiayaLain as $key => $biayaLain){
                    $sheet->getStyle('E'.$startRowBiayaLain)->getNumberFormat()->setFormatCode($localeCurrencyMask);
                    $sheet->getStyle('F'.$startRowBiayaLain)->getNumberFormat()->setFormatCode($localeCurrencyMask);
                    $startRowBiayaLain++;
                }
                
                $sheet->getStyle('F'.$totalRowBahan)->getNumberFormat()->setFormatCode($localeCurrencyMask);
                

                // Set border
                $sheet->getStyle('B2:F6')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('B8:F10')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('B'.$totalRowBahan.':F'.$totalRowBahan)->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('B'.$startHeaderBiayaLain.':F'.$totalHeaderBiayaLain)->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('B'.$startRowBiayaLain.':F'.$akhirRowBiayaLain)->getBorders()->getAllBorders()->setBorderStyle('thin');
                
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 6,
            'C' => 27,
            'D' => 10,
            'E' => 11,
            'F' => 12,
        ];
    }
}
