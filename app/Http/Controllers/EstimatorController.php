<?php

namespace App\Http\Controllers;

use App\Models\Bahan;

use App\Models\Barang;

use App\Models\Employee;
use App\Exports\BPExport;
use App\Models\BiayaLain;
use App\Models\DataKerja;
use App\Models\DataAntrian;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Exports\AntrianExport;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EstimatorController extends Controller
{
    public function laporanPenugasan()
    {
        return view('page.estimator.laporan-penugasan');
    }

    public function laporanWorkshopExcel()
    {
        return Excel::download(new AntrianExport, 'laporan-workshop.xlsx');
    }

    public function laporanPenugasanJson(Request $request)
    {
        $periode = date('Y') . '-' . $request->get('periode');
        if($request->get('periode')){
            $awal = date('Y-m-01', strtotime($periode));
            $akhir = date('Y-m-t', strtotime($periode));
        }else{
            $awal = date('Y-m-01');
            $akhir = date('Y-m-d');
        }

        $antrians = Barang::with(['user.sales', 'kategori', 'job', 'dataKerja', 'designQueue.designer'])
        ->whereHas('antrian', function($query) use ($awal, $akhir){
            $query->whereBetween('created_at', [$awal, $akhir]);
        })->get();

        // Kumpulkan semua ID employee yang unik
        $employeeIds = [];
        foreach ($antrians as $antrian) {
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->operator_id ?? ''));
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->finishing_id ?? ''));
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->qc_id ?? ''));
        }
        $employeeIds = array_unique(array_filter($employeeIds, function($id) { return $id !== 'r'; }));

        // Ambil data semua employee yang dibutuhkan sekaligus
        $employees = Employee::whereIn('id', $employeeIds)->pluck('name', 'id');

        return Datatables::of($antrians)
            ->addIndexColumn()
            ->addColumn('ticket_order', function ($antrian) {
                return '<a class="text-primary" href="'.route('biaya.produksi', $antrian->id).'">'.$antrian->ticket_order.'</a>';
            })
            ->addColumn('sales', function ($antrian) {
                return $antrian->user->sales->sales_name;
            })
            ->addColumn('kategori', function ($antrian) {
                return $antrian->kategori->nama_kategori;
            })
            ->addColumn('nama_produk', function ($antrian) {
                return $antrian->job->job_name;
            })
            ->addColumn('qty', function ($antrian) {
                return $antrian->qty;
            })
            ->addColumn('tgl_mulai', function ($antrian) {
                return $antrian->dataKerja->tgl_mulai ?? '<span class="text-danger">BELUM DITUGASKAN</span>';
            })
            ->addColumn('tgl_selesai', function ($antrian) {
                return $antrian->dataKerja->tgl_selesai ?? '<span class="text-danger">BELUM DITUGASKAN</span>';
            })
            ->addColumn('desainer', function ($antrian) {
                if($antrian->design_queue_id == null){
                    return '<span class="text-danger">DESAINER KOSONG</span>';
                }else{
                    return $antrian->designQueue->designer->name;
                }
            })
            ->addColumn('operator', function ($antrian) use ($employees){
                if($antrian->dataKerja->operator_id == null){
                    return '<span class="text-danger">OPERATOR KOSONG</span>';
                }else{
                    //explode string operator
                    $operator = explode(',', $antrian->dataKerja->operator_id);
                    $namaOperator = [];
                    foreach($operator as $o){
                        if($o == 'r'){
                            $namaOperator[] = "<span class='text-primary'>Rekanan</span>";
                        }else{
                            $namaOperator[] = $employees[$o] ?? '';
                        }
                    }
                    return implode(', ', $namaOperator);
                }
            })
            ->addColumn('finishing', function ($antrian) use ($employees){
                if($antrian->dataKerja->finishing_id == null){
                    return '<span class="text-danger">FINISHING KOSONG</span>';
                }else{
                    //explode string finishing
                    $finishing = explode(',', $antrian->dataKerja->finishing_id);
                    $namaFinishing = [];
                    foreach($finishing as $f){
                        if($f == 'r'){
                            $namaFinishing[] = "<span class='text-primary'>Rekanan</span>";
                        }else{
                            $namaFinishing[] = $employees[$f] ?? ''; 
                        }
                    }
                    return implode(', ', $namaFinishing);
                }
            })
            ->addColumn('qc', function ($antrian) use ($employees){
                if($antrian->dataKerja->qc_id == null){
                    return '<span class="text-danger">QC KOSONG</span>';
                }else{
                    //explode string qc
                    $qc = explode(',', $antrian->dataKerja->qc_id);
                    $namaQc = [];
                    foreach($qc as $q){
                        $namaQc[] = $employees[$q] ?? ''; 
                    }
                    return implode(', ', $namaQc);
                }
            })
            ->addColumn('omset', function ($antrian) {
                return 'Rp'. number_format($antrian->qty * $antrian->price,0,',','.');
            })
            ->addColumn('status', function ($antrian) {
                if($antrian->status == 1){
                    return '<span class="font-weight-bold text-warning">DIPROSES</span>';
                }else{
                    return '<span class="font-weight-bold text-success">SELESAI</span>';
                }
            })
            ->addColumn('action', function ($antrian) {
                return '<a href="'.route('biaya.produksi', $antrian->id).'" class="btn btn-sm btn-primary">Lihat BP</a>';
            })
            ->rawColumns(['ticket_order','operator', 'finishing', 'qc', 'desainer', 'status', 'tgl_mulai', 'tgl_selesai', 'omset', 'action'])
            ->make();
    }

    public function unduhBPExcel($id)
    {
        $barang = Barang::find($id);
        $filename = 'BP'. '_' . $barang->ticket_order . '_' . $barang->job->job_name . '.xlsx';
        return Excel::download(new BPExport($id), $filename);
    }

    public function biayaProduksi($id)
    {
        $canViewModal = Gate::allows('view-tambah-bahan-produksi');

        $barang = Barang::find($id);

        $bahan = Bahan::where('barang_id', $id)->get();
        $totalProduksi = 0;
        foreach($bahan as $b){
            $totalProduksi += $b->harga * $b->qty;
        }

        $biayaLainnya = BiayaLain::all();

        return view('page.estimator.biaya-produksi', compact('barang', 'bahan', 'totalProduksi', 'biayaLainnya', 'canViewModal'));
    }

    public function tambahBahanProduksi(Request $request)
    {
        $bahan = Bahan::create([
            'ticket_order' => $request->ticketOrder,
            'nama_bahan' => $request->nama_bahan,
            'barang_id' => $request->idBarang,
            'qty' => $request->qty,
            'harga' => CustomHelper::removeCurrencyFormat($request->harga),
            'note' => $request->note ?? null
        ]);

        return redirect()->route('biaya.produksi', $request->idBarang)->with('message', 'Bahan berhasil ditambahkan!');
    }
}
