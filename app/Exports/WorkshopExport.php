<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkshopExport implements FromView, WithStyles, ShouldAutoSize
{
    use Exportable;

    public function view(): View
    {
        $awal = date('Y-m-01');
        $akhir = date('Y-m-d');

        $stempels = Barang::whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })
        ->where('kategori_id', 1)
        ->get();

        $nonStempels = Barang::whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })
        ->where('kategori_id', 2)
        ->get();

        $advertisings = Barang::whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })
        ->where('kategori_id', 3)
        ->get();

        $digitals = Barang::whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })
        ->where('kategori_id', 4)
        ->get();

        $servis = Barang::whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })
        ->where('kategori_id', 5)
        ->get();

        return view('page.antrian-workshop.laporan-workshop-excel', [
            'stempels' => $stempels,
            'nonStempels' => $nonStempels,
            'advertisings' => $advertisings,
            '$digitalPrintings' => $digitals,
            'servis' => $servis
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header tabel (baris ke-6)
        $sheet->getStyle('A6:I6')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00', // Warna kuning
                ],
            ],
        ]);

        // Style untuk border seluruh tabel (mulai baris ke-6)
        $sheet->getStyle('A6:I'.$sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style untuk format rupiah
        $sheet->getStyle('F7:I'.$sheet->getHighestRow())->getNumberFormat()->setFormatCode('#,##0'); 
    }
}
