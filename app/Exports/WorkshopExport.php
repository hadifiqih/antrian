<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkshopExport implements FromView, WithStyles, ShouldAutoSize
{
    use Exportable;

    Public function __construct()
    {
        $this->data = Barang::with(['dataKerja', 'job', 'user', 'designQueue'])
                        ->where('ticket_order', '!=', null)
                        ->get();
    }

    public function styles(Worksheet $sheet)
    {
        $stempels = $this->data->where('kategori_id', 1)->count();
        $nonStempels = $this->data->where('kategori_id', 2)->count();
        $advertisings = $this->data->where('kategori_id', 3)->count();
        $digitals = $this->data->where('kategori_id', 4)->count();
        $servis = $this->data->where('kategori_id', 5)->count();

        $tidakGuna = 5;
        $headerNon = $stempels + $tidakGuna;
        $headerAdv = $nonStempels + $tidakGuna + $headerNon - 1;
        $headerDig = $advertisings + $tidakGuna + $headerAdv - 1;
        $headerSer = $digitals + $tidakGuna + $headerDig - 1;

        // Style untuk header tabel (baris ke-6)
        $sheet->getStyle('A1:M1')->applyFromArray([
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

        // Style untuk header tabel non stempel
        $sheet->getStyle('A'.$headerNon.':M'.$headerNon)->applyFromArray([
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

        // Style untuk header tabel advertising
        $sheet->getStyle('A'.$headerAdv.':M'.$headerAdv)->applyFromArray([
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

        // Style untuk header tabel digital
        $sheet->getStyle('A'.$headerDig.':M'.$headerDig)->applyFromArray([
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

        // Style untuk header tabel servis
        $sheet->getStyle('A'.$headerSer.':M'.$headerSer)->applyFromArray([
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
        $sheet->getStyle('A1:M'.$sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $localeCurrencyMask = '[$Rp-421]#,##0';
        // Style untuk format rupiah
        $sheet->getStyle('M3:M'.$sheet->getHighestRow())->getNumberFormat()->setFormatCode($localeCurrencyMask); 
    }

    public function view(): View
    {
        $stempels = $this->data->where('kategori_id', 1);
        $nonStempels = $this->data->where('kategori_id', 2);
        $advertisings = $this->data->where('kategori_id', 3);
        $digitals = $this->data->where('kategori_id', 4);
        $servis = $this->data->where('kategori_id', 5);

        return view('page.antrian-workshop.laporan-workshop-excel', [
            'stempels' => $stempels,
            'nonStempels' => $nonStempels,
            'advertisings' => $advertisings,
            'digitals' => $digitals,
            'servis' => $servis,
            'totalStempel' => $totalStempel,
            'totalNonStempel' => $totalNonStempel,
            'totalAdvertising' => $totalAdvertising,
            'totalDigital' => $totalDigital,
            'totalServis' => $totalServis
        ]);
    }
}
