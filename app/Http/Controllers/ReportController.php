<?php

namespace App\Http\Controllers;

use PDF;
use QrCode;
use Carbon\Carbon;
use Dompdf\Dompdf;

use App\Models\Bahan;
use App\Models\Order;
use App\Models\Sales;
use App\Models\Barang;
use App\Models\Antrian;
use App\Models\Machine;
use App\Models\DataKerja;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use Mike42\Escpos\Printer;
use App\Models\DataAntrian;
use Illuminate\Http\Request;
use App\Models\BiayaProduksi;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\CapabilityProfile;
use App\Http\Resources\ReportResource;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function notaOrderPDF($id)
    {
        $order = DataAntrian::where('ticket_order', $id)->first();

        $items = Barang::where('ticket_order', $id)->get();
        //HITUNG TOTAL HARGA
        $totalHarga = 0;
        $totalPacking = 0;
        $totalOngkir = 0;
        $totalPasang = 0;
        $diskon = 0;
        foreach ($items as $item) {
            $totalHarga += $item->price * $item->qty;
        }

        $infoBayar = Pembayaran::where('ticket_order', $id)->first();
        $totalPacking = $infoBayar->biaya_packing;
        $totalPasang = $infoBayar->biaya_pasang;
        $diskon = $infoBayar->diskon;
        
        $infoPengiriman = Pengiriman::where('ticket_order', $id)->first();
        $totalOngkir = $infoPengiriman->ongkir;

        $grandTotal = $totalHarga + $totalPacking + $totalOngkir + $totalPasang - $diskon;
        $sisaTagihan = $grandTotal - $infoBayar->dibayarkan;

        $qrCode = QrCode::size(70)->generate($order->ticket_order);

        $pdf = PDF::loadview('page.report.form-nota-order2', compact('order', 'items', 'totalHarga', 'totalPacking', 'totalOngkir', 'totalPasang', 'diskon', 'grandTotal', 'sisaTagihan', 'infoBayar', 'qrCode'))->setPaper('a4', 'portrait');
        return $pdf->stream($order->ticket_order . "_" . $order->order->title . '_nota-order.pdf');
    }

    public function notaOrderView($id)
    {
        $order = DataAntrian::where('ticket_order', $id)->first();

        $items = Barang::where('ticket_order', $id)->get();
        //HITUNG TOTAL HARGA
        $totalHarga = 0;
        $totalPacking = 0;
        $totalOngkir = 0;
        $totalPasang = 0;
        $diskon = 0;
        foreach ($items as $item) {
            $totalHarga += $item->price * $item->qty;
        }

        $infoBayar = Pembayaran::where('ticket_order', $id)->first();
        $totalPacking = $infoBayar->biaya_packing;
        $totalPasang = $infoBayar->biaya_pasang;
        $diskon = $infoBayar->diskon;
        
        $infoPengiriman = Pengiriman::where('ticket_order', $id)->first();
        $totalOngkir = $infoPengiriman->ongkir;

        $grandTotal = $totalHarga + $totalPacking + $totalOngkir + $totalPasang - $diskon;
        $sisaTagihan = $grandTotal - $infoBayar->dibayarkan;

        $qrCode = QrCode::size(70)->generate($order->ticket_order);

        return view('page.report.view-nota-order', compact('order', 'items', 'totalHarga', 'totalPacking', 'totalOngkir', 'totalPasang', 'diskon', 'grandTotal', 'sisaTagihan', 'infoBayar', 'qrCode'));
    }

    public function notaOrder($id)
    {
        //function pembagian kolom
        function buatBaris1Kolom($kolom1)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 45;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = count($kolom1Array);

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }

        function buatBaris3Kolom($kolom1, $kolom2, $kolom3)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 15;
            $lebar_kolom_2 = 11;
            $lebar_kolom_3 = 17;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ", STR_PAD_RIGHT);
                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ", STR_PAD_BOTH);

                $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }

        function buatBaris2Kolom($kolom1, $kolom2)
        {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 20;
            $lebar_kolom_2 = 25;

            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);

            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);

            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array));

            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();

            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {

                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ", STR_PAD_RIGHT);
                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ", STR_PAD_LEFT);

                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }

        $order = DataAntrian::with(['customer', 'sales', 'pembayaran', 'pengiriman'])
                        ->where('ticket_order', $id)
                        ->first();
        $items = Barang::with(['job'])->where('ticket_order', $id)->get();
        $sales = Sales::where('id', $order->sales_id)->first();

        //HITUNG TOTAL HARGA
        $totalHarga = 0;
        $totalPacking = 0;
        $totalOngkir = 0;
        $totalPasang = 0;
        $diskon = 0;

        foreach ($items as $item) {
            $totalHarga += $item->price * $item->qty;
        }

        $infoBayar = Pembayaran::where('ticket_order', $id)->orderBy('created_at', 'desc')->first();
        $totalPacking = $infoBayar->biaya_packing;
        $totalPasang = $infoBayar->biaya_pasang;
        $diskon = $infoBayar->diskon;

        $infoPengiriman = Pengiriman::where('ticket_order', $id)->first();
        $totalOngkir = $infoPengiriman->ongkir ?? 0;

        $grandTotal = $totalHarga + $totalPacking + $totalOngkir + $totalPasang - $diskon;
        $sisaTagihan = $grandTotal - $infoBayar->dibayarkan;

        //print nota order dengan menggunakan printer thermal
        $connector = new WindowsPrintConnector("POS-80");
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("STRUK PEMBELIAN\n");
        $printer->text($sales->sales_name."\n");
        $printer->text($sales->address."\n");
        $printer->text("WA. ".$sales->sales_phone."\n");
        $printer->text("==============================================\n");
        $printer->setEmphasis(true);
        $printer->text("No. Antrian : ".$order->ticket_order."\n");
        $printer->setEmphasis(false);
        $printer->text("==============================================\n");
        $printer->text(buatBaris2Kolom($order->created_at->format('d F Y'), $order->created_at->format('H:i')));
        $printer->text(buatBaris2Kolom("Pelanggan" , $order->customer->nama));
        $printer->text(buatBaris2Kolom("Kasir" , $order->sales->sales_name));
        $printer->text();
        $printer->text("==============================================\n");
        foreach ($items as $item) {
            $printer->text(buatBaris1Kolom($item->job->job_name));
            $printer->text(buatBaris3Kolom($item->qty . "x", '@' . number_format($item->price, 0, ',', '.') , number_format($item->price * $item->qty, 0, ',', '.')));
        }
        $printer->text("==============================================\n");
        $printer->text(buatBaris3Kolom("Total Harga" , 'Rp.', number_format($totalHarga, 0, ',', '.')));
        $printer->text(buatBaris3Kolom("Biaya Packing" , 'Rp.', number_format($totalPacking, 0, ',', '.')));
        $printer->text(buatBaris3Kolom("Biaya Ongkir" , 'Rp.', number_format($totalOngkir, 0, ',', '.')));

        if ($totalPasang != 0) {
            $printer->text(buatBaris3Kolom("Biaya Pasang" , 'Rp.', number_format($totalPasang, 0, ',', '.')));
        }

        if ($diskon != 0) {
            $printer->text(buatBaris3Kolom("Diskon" ,'Rp.', number_format($diskon, 0, ',', '.')));
        }

        $printer->text("==============================================\n");
        $printer->text(buatBaris3Kolom("Grand Total" ,'Rp.', number_format($grandTotal, 0, ',', '.')));
        $printer->text(buatBaris3Kolom("Dibayarkan" ,'Rp.', number_format($infoBayar->dibayarkan, 0, ',', '.')));
        $printer->text("==============================================\n");
        $printer->text(buatBaris3Kolom("Sisa Tagihan" ,'Rp.', number_format($sisaTagihan, 0, ',', '.')));
        $printer->text("==============================================\n");
        $printer->text("-- Terima Kasih --\nSelamat Datang Kembali\n");
        $printer->text("----------------------------------------------\n");
        $printer->text("Tanggal Cetak : ". date('d F Y H:i:s'));

        $printer->cut();
        $printer->close();
    }

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

    public function fakturPenjualan($tiket)
    {
        $antrian = DataAntrian::where('ticket_order', $tiket)->first();
        $items = Barang::where('ticket_order', $tiket)->get();
        $sales = Sales::find($antrian->sales_id);

        $totalHarga = 0;
        $totalPacking = 0;
        $totalOngkir = 0;
        $totalPasang = 0;
        $pajak = 0;
        $diskon = 0;

        foreach ($items as $item) {
            $totalHarga += $item->price * $item->qty;
        }
        
        $diskon = $antrian->pembayaran->diskon;
        $pajak = $antrian->ppn + $antrian->pph;

        $totalPasang = $antrian->pembayaran->biaya_pasang;
        $totalOngkir = $antrian->pengiriman->count() > 0  ? $antrian->pengiriman->ongkir : 0;
        $totalPacking = $antrian->pembayaran->biaya_packing;

        $grandTotal = $totalHarga + $totalPasang + $totalOngkir + $totalPacking + $pajak - $diskon;
        $sisaTagihan = $grandTotal - $antrian->pembayaran->dibayarkan;

        return view('page.report.faktur-penjualan', compact('sales' ,'antrian', 'items', 'totalHarga', 'totalPacking', 'totalOngkir', 'totalPasang', 'diskon', 'grandTotal', 'sisaTagihan'));
    }

    public function index()
    {
        // $tanggalAwal adalah selalu tanggal 1 dari bulan yang dipilih
        $tanggalAwal = date('Y-m-01 00:00:00');
        // $tanggalAkhir adalah selalu tanggal sekarang dari bulan yang dipilih
        $tanggalAkhir = date('Y-m-d 23:59:59');

        $antrians = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
            ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->get();

        $totalOmset = 0;
        foreach ($antrians as $antrian) {
            $totalOmset += $antrian->omset;
        }

        return new ReportResource(true, 'Data omset global sales berhasil diambil', $antrians, $totalOmset);
    }

    public function showJsonByTicket($id)
    {
        $antrians = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'machine')
                    ->where('ticket_order', $id)
                    ->first();

        return response()->json($antrians);
    }

    public function showOrderByTicket($id)
    {
        $orders = Order::with('antrian', 'sales', 'job', 'employee')
                    ->where('ticket_order', $id)
                    ->first();

        return response()->json($orders);
    }

    public function pilihTanggal()
    {
        return view('page.antrian-workshop.pilih-tanggal');
    }

    public function pilihTanggalDesain()
    {
        return view('page.antrian-desain.pilih-tanggal');
    }

    public function exportLaporanDesainPDF(Request $request)
    {

        $tanggal = $request->tanggal;
        //Mengambil data antrian dengan relasi customer, sales, payment, operator, finishing, job, order pada tanggal yang dipilih dan menghitung total omset dan total order
        $antrians = Antrian::with('customer', 'sales', 'payment', 'operator', 'finishing', 'job', 'order')
            ->whereDate('created_at', $tanggal)
            ->get();

        $totalOmset = 0;
        $totalQty = 0;
        foreach ($antrians as $antrian) {
            $totalOmset += $antrian->omset;
            $totalQty += $antrian->qty_produk;
        }

        $pdf = PDF::loadview('page.antrian-workshop.laporan-desain', compact('antrians', 'totalOmset', 'totalQty', 'tanggal'));
        return $pdf->stream($tanggal . '-laporan-desain.pdf');
        // return $pdf->download($tanggal . '-laporan-workshop.pdf');
    }

    public function exportLaporanWorkshopPDF(Request $request)
    {
        $tempat = $request->input('tempat_workshop');
        // $tanggalAwal adalah selalu tanggal 1 dari bulan yang dipilih
        $tanggalAwal = date('Y-m-01 00:00:00');
        // $tanggalAkhir adalah selalu tanggal sekarang dari bulan yang dipilih
        $tanggalAkhir = date('Y-m-d 23:59:59');

        $antrianStempel = Barang::where('kategori_id', '1')
        ->whereHas('antrian', function ($query) use ($tempat, $tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->whereHas('sales', function ($subquery) use ($tempat) {
                $subquery->where('cabang_id', $tempat);
            })
            ->where(function ($query) {
                $query->where('status', '1')->orWhere('status', '2');
            });
        })
        ->get();

        $antrianAdvertising = Barang::where('kategori_id', '3')
        ->whereHas('antrian', function ($query) use ($tempat, $tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->whereHas('sales', function ($subquery) use ($tempat) {
                $subquery->where('cabang_id', $tempat);
            })
            ->where(function ($query) {
                $query->where('status', '1')->orWhere('status', '2');
            });
        })
        ->get();

        $antrianNonStempel = Barang::where('kategori_id', '2')
        ->whereHas('antrian', function ($query) use ($tempat, $tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->whereHas('sales', function ($subquery) use ($tempat) {
                $subquery->where('cabang_id', $tempat);
            })
            ->where(function ($query) {
                $query->where('status', '1')->orWhere('status', '2');
            });
        })
        ->get();

        $antrianDigiPrint = Barang::where('kategori_id', '4')
        ->whereHas('antrian', function ($query) use ($tempat, $tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->whereHas('sales', function ($subquery) use ($tempat) {
                $subquery->where('cabang_id', $tempat);
            })
            ->where(function ($query) {
                $query->where('status', '1')->orWhere('status', '2');
            });
        })
        ->get();

        //buat beberapa variabel dengan nilai 0 untuk menampung total omset dan total order
        $totalOmsetStempel = 0;
        $totalQtyStempel = 0;

        $totalOmsetAdvertising = 0;
        $totalQtyAdvertising = 0;

        $totalOmsetNonStempel = 0;
        $totalQtyNonStempel = 0;

        $totalOmsetDigiPrint = 0;
        $totalQtyDigiPrint = 0;

        //looping untuk menghitung total omset dan total order
        foreach ($antrianStempel as $antrian) {
            $totalOmsetStempel += $antrian->price * $antrian->qty;
            $totalQtyStempel += $antrian->qty;
        }

        foreach ($antrianAdvertising as $antrian) {
            $totalOmsetAdvertising += $antrian->price * $antrian->qty;
            $totalQtyAdvertising += $antrian->qty;
        }

        foreach ($antrianNonStempel as $antrian) {
            $totalOmsetNonStempel += $antrian->price * $antrian->qty;
            $totalQtyNonStempel += $antrian->qty;
        }

        foreach ($antrianDigiPrint as $antrian) {
            $totalOmsetDigiPrint += $antrian->price * $antrian->qty;
            $totalQtyDigiPrint += $antrian->qty;
        }

        $pdf = PDF::loadview('page.antrian-workshop.laporan-workshop', compact('tanggalAwal', 'tanggalAkhir', 'totalOmsetStempel', 'totalQtyStempel', 'totalOmsetAdvertising', 'totalQtyAdvertising', 'totalOmsetNonStempel', 'totalQtyNonStempel', 'totalOmsetDigiPrint', 'totalQtyDigiPrint', 'antrianStempel', 'antrianNonStempel', 'antrianAdvertising', 'antrianDigiPrint', 'tempat'))->setPaper('folio', 'landscape');
        return $pdf->stream($tempat .  '_Laporan_Workshop.pdf');
    }

    public function cetakEspk($id)
    {
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        $order = Order::where('ticket_order', $id)->first();
        $dataKerja = DataKerja::where('ticket_order', $id)->first();
        $customer = $antrian->customer;
        $barang = Barang::where('ticket_order', $id)->get();

        $pdf = PDF::loadview('page.antrian-workshop.cetak-spk-workshop', compact('antrian', 'order', 'dataKerja', 'customer', 'barang'))->setPaper('folio', 'portrait');
        return $pdf->stream("Adm_" . $antrian->ticket_order . "_" . $antrian->order->title . '_espk.pdf');
        // return view('page.antrian-workshop.cetak-spk-workshop', compact('antrian'));
    }

    public function reportSales()
    {
        $salesId = auth()->user()->id;
        $bulan = date('m');

        $totalOmset = 0;
        $totalOmsetToday = 0;

        $mulai = date('Y-m-01 00:00:00');
        $selesai = date('Y-m-d 23:59:59');

        $mulaiToday = date('Y-m-d 00:00:00');
        $selesaiToday = date('Y-m-d 23:59:59');

        $antrians = Barang::whereHas('antrian', function ($query) use ($mulai, $selesai, $salesId) {
            $query->whereBetween('created_at', [$mulai, $selesai]);
        })
        ->where('user_id', $salesId)
        ->get();
        
        foreach ($antrians as $antrian) {
            $totalOmset += $antrian->price * $antrian->qty;
        }

        //--------------------------------------------
        $antriansToday = Barang::whereHas('antrian', function ($query) use ($mulaiToday, $selesaiToday, $salesId) {
            $query->whereBetween('created_at', [$mulaiToday, $selesaiToday]);
        })
        ->where('user_id', $salesId)
        ->get();

        foreach ($antriansToday as $antrian) {
            $totalOmsetToday += $antrian->price * $antrian->qty;
        }

        return view('page.antrian-workshop.ringkasan-sales', compact('antrians', 'totalOmset', 'totalOmsetToday', 'bulan'));
    }

    public function reportFormOrder($id)
    {
     $antrian = Antrian::with('customer', 'sales', 'payment', 'operator', 'finishing', 'job', 'order')
            ->where('ticket_order', $id)
            ->first();
     // return view('page.antrian-workshop.form-order', compact('antrian'));
        $pdf = PDF::loadview('page.antrian-workshop.form-order', compact('antrian'))->setPaper('a4', 'portrait');
        return $pdf->stream($antrian->ticket_order . "_" . $antrian->order->title . '_form-order.pdf');
    }

    public function omsetGlobalSales()
    {
        //melakukan perulangan tanggal pada bulan ini, menyimpannya dalam array
        $dateRange = [];
        $dateAwal = date('Y-m-01');
        $dateAkhir = date('Y-m-d');
        $date = $dateAwal;

        while (strtotime($date) <= strtotime($dateAkhir)) {
            $dateRange[] = $date;
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        return view('page.report.omset-global-sales', compact('dateRange'));
    }

    public function omsetPerCabang()
    {
        //melakukan perulangan tanggal pada bulan ini, menyimpannya dalam array
        $dateRange = [];
        $dateAwal = date('Y-m-01');
        $dateAkhir = date('Y-m-d');
        $date = $dateAwal;

        while (strtotime($date) <= strtotime($dateAkhir)) {
            $dateRange[] = $date;
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        return view('page.report.omset-per-cabang', compact('dateRange'));
    }

    public function omsetPerProduk()
    {
        //melakukan perulangan tanggal pada bulan ini, menyimpannya dalam array
        $dateRange = [];
        $dateAwal = date('Y-m-01 00:00:00');
        $dateAkhir = date('Y-m-d 23:59:59');
        $date = $dateAwal;

        while (strtotime($date) <= strtotime($dateAkhir)) {
            $dateRange[] = $date;
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        return view('page.report.omset-per-produk', compact('dateRange'));
    }

    public function ringkasanOmsetSales(Request $request)
    {
        if ($request->query('month') != null) {
            $month = $request->query('month');
            $year = date('Y'); // Mengambil tahun saat ini
        
            $mulai = date('Y-' . $month . '-01 00:00:00');
            $akhirBulan = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $selesai = date('Y-' . $month . '-' . $akhirBulan . ' 23:59:59');
        } else {
            $mulai = date('Y-m-01 00:00:00');
            $selesai = date('Y-m-d 23:59:59');
        }

        $query = Barang::with(['antrian', 'job', 'customer'])
            ->whereHas('antrian', function ($query) use ($mulai, $selesai) {
                $query->whereBetween('created_at', [$mulai, $selesai]);
            });

        if(auth()->user()->role_id == 11) {
            $user = auth()->user()->id;
            $query->where('user_id', $user);   
        }

        $barangs = $query->get();

        return Datatables::of($barangs)
        ->addIndexColumn()
        ->addColumn('tanggal', function ($row) {
            return $row->antrian->created_at->format('d F Y');
        })
        ->addColumn('nama_pelanggan', function ($row) {
            return $row->customer->nama ?? null;
        })
        ->addColumn('nama_produk', function ($row) {
            return $row->job->job_name;
        })
        ->addColumn('total_omset', function ($row) {
            return 'Rp'. number_format($row->price * $row->qty, 0, ',', '.');
        })
        ->addColumn('status_pengerjaan', function ($row) {
            if ($row->antrian->status == 0) {
                return 'Belum Dikerjakan';
            } elseif ($row->antrian->status == 1) {
                return 'Sedang Dikerjakan';
            } elseif ($row->antrian->status == 2) {
                return 'Selesai';
            }
        })
        ->rawColumns(['nama_pelanggan', 'nama_produk', 'total_omset', 'status_pengerjaan'])
        ->make(true);
    }

    public function mesin()
    {
        $machine = Machine::all();

        return response()->json($machine);
    }

    //variabel global untuk menyimpan biaya produksi
    public $biayaSales = 0.03;
    public $biayaDesain = 0.02;
    public $biayaPenanggungJawab = 0.03;
    public $biayaPekerja = 0.05;
    public $biayaBPJS = 0.025;
    public $biayaTransport = 0.01;
    public $biayaOverhead = 0.025;
    public $biayaListrik = 0.02;

    public function tampilBP($id)
    {
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        $barangs = Barang::where('ticket_order', $id)->get();
        $biaya = BiayaProduksi::where('ticket_order', $id)->first();
        $bahans = Bahan::where('ticket_order', $id)->get();

        $dataKerja = DataKerja::where('ticket_order', $id)->first();

        $total = 0;
        $omset = 0;
        
        foreach ($barangs as $barang) {
            $omset += $barang->price * $barang->qty;
        }
        foreach ($bahans as $bahan) {
            $total += $bahan->harga;
        }

        $totalBiayaSales = 0;
        $totalBiayaDesain = 0;
        $totalBiayaPenanggungJawab = 0;
        $totalBiayaPekerja = 0;
        $totalBiayaBPJS = 0;
        $totalBiayaTransport = 0;
        $totalBiayaOverhead = 0;
        $totalBiayaListrik = 0;
        $totalBiayaLain = 0;

        if(!isset($biaya) || $biaya == null) {
            $totalBiayaSales = $omset * $biayaSales;
            $totalBiayaDesain = $omset * $biayaDesain;
            $totalBiayaPenanggungJawab = $omset * $biayaPenanggungJawab;
            $totalBiayaPekerja = $omset * $biayaPekerja;
            $totalBiayaBPJS = $omset * $biayaBPJS;
            $totalBiayaTransport = $omset * $biayaTransport;
            $totalBiayaOverhead = $omset * $biayaOverhead;
            $totalBiayaListrik = $omset * $biayaListrik;
        }else{
            $totalBiayaSales = number_format($biaya->biaya_sales,0,',','.');
            $totalBiayaDesain = number_format($biaya->biaya_desain,0,',','.');
            $totalBiayaPenanggungJawab = number_format($biaya->biaya_penanggung_jawab,0,',','.');
            $totalBiayaPekerja = number_format($biaya->biaya_pekerjaan,0,',','.');
            $totalBiayaBPJS = number_format($biaya->biaya_bpjs,0,',','.');
            $totalBiayaTransport = number_format($biaya->biaya_transportasi,0,',','.');
            $totalBiayaOverhead = number_format($biaya->biaya_overhead,0,',','.');
            $totalBiayaListrik = number_format($biaya->biaya_alat_listrik,0,',','.');

            //total biaya mulai dari biaya sales sampai biaya listrik
            $totalBiayaAll = $biaya->biaya_sales + $biaya->biaya_desain + $biaya->biaya_penanggung_jawab + $biaya->biaya_pekerjaan + $biaya->biaya_bpjs + $biaya->biaya_transportasi + $biaya->biaya_overhead + $biaya->biaya_alat_listrik;
            $totalBiayaAllFormatted = number_format($totalBiayaAll,0,',','.');
        }

        $totalProduksi = $total + $totalBiayaAll;
        $profit = $omset - $totalProduksi;
        $persenBiayaProduksi = ($totalProduksi / $omset) * 100;
        $persenProfit = 100 - $persenBiayaProduksi;
        //jadikan format persentasi $persenBiayaProduksi
        $persenBiayaProduksi = number_format($persenBiayaProduksi, 2, ',', '.');
        $persenProfit = number_format($persenProfit, 2, ',', '.');

        $pdf = PDF::loadview('page.report.unduh-bp', compact('omset','barangs','antrian', 'biaya', 'bahans', 'total', 'omset', 'dataKerja', 'totalBiayaSales', 'totalBiayaDesain', 'totalBiayaPenanggungJawab', 'totalBiayaPekerja', 'totalBiayaBPJS', 'totalBiayaTransport', 'totalBiayaOverhead', 'totalBiayaListrik', 'totalBiayaAllFormatted', 'totalProduksi', 'profit', 'persenBiayaProduksi', 'persenProfit'))->setPaper('a4', 'portrait');
        return $pdf->stream($antrian->ticket_order . "_" . $antrian->order->title . '_biaya-produksi.pdf');
    }
}
