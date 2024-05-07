<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokBahan;

use App\Models\MutasiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllProducts()
    {
        $products = Produk::getProducts();
        return response()->json($products);
    }

    public function daftarStok()
    {
        return view('page.stok.daftar-stok');
    }

    public function daftarMutasi()
    {
        return view('page.stok.daftar-mutasi-stok');
    }

    public function mutasiStok()
    {
        return view('page.stok.mutasi-stok');
    }

    public function simpanMutasi(Request $request)
    {
        $kategori = $request->kategori;
        $cabang = auth()->user()->cabang_id;
        $produk = Produk::find($request->produkId);

        try{
            $mutasi = new MutasiStok;
            $mutasi->produk_id = $produk->id;
            $mutasi->cabang_id = $cabang;
            $mutasi->kategori_mutasi = $request->kategori;
            $mutasi->jenis_mutasi = $request->jenis;
            $mutasi->jumlah_stok = $request->jumlah;
            $mutasi->keterangan = $request->keterangan;
            $mutasi->save();
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Mutasi stok gagal disimpan');
        }

        try{
            $stok = StokBahan::where('produk_id', $produk->id)->where('cabang_id', $cabang)->first();
            if($kategori == 'masuk'){
                $stok->jumlah_stok = $stok->jumlah_stok + $request->jumlah;
            }else{
                $stok->jumlah_stok = $stok->jumlah_stok - $request->jumlah;
            }
            $stok->save();
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Tidak berhasil melakukan update stok produk.');
        }

        return redirect()->route('daftarMutasi')->with('success', 'Mutasi stok berhasil disimpan.');
    }

    public function daftarStokJson()
    {
        function getStokMasuk($id)
        {
            $cabang = auth()->user()->cabang_id;
            $mutasi = MutasiStok::where('produk_id', $id)->where('cabang_id', $cabang)->where('kategori_mutasi', 'masuk')->sum('jumlah_stok');
            return $mutasi;
        }

        function getStokKeluar($id)
        {
            $cabang = auth()->user()->cabang_id;
            $mutasi = MutasiStok::where('produk_id', $id)->where('cabang_id', $cabang)->where('kategori_mutasi', 'keluar')->sum('jumlah_stok');
            return $mutasi;
        }

        function getStok($id)
        {
            $cabang = auth()->user()->cabang_id;
            $stok = StokBahan::where('produk_id', $id)->where('cabang_id', $cabang)->first();
            return $stok->jumlah_stok;
        }

        //yajra datatable
        $stocks = Produk::all();
        
        return Datatables::of($stocks)
            ->addColumn('sku', function ($stock) {
                return $stock->kode_produk;
            })
            ->addColumn('nama', function ($stock) {
                return $stock->nama_produk;
            })
            ->addColumn('masuk', function ($stock) {
                return getStokMasuk($stock->id) ?? 0;
            })
            ->addColumn('terjual', function ($stock) {
                return getStokKeluar($stock->id) ?? 0;
            })
            ->addColumn('stok', function ($stock) {
                return getStok($stock->id);
            })
            ->addColumn('satuan', function ($stock) {
                return 'pcs';
            })
            ->make(true);
    }

    public function mutasiStokJson()
    {
        //mutasi stok bulan ini
        $cabang = auth()->user()->cabang_id;
        $mutasi = MutasiStok::where('cabang_id', $cabang)->whereMonth('created_at', date('m'))->get();

        //yajra datatable
        return Datatables::of($mutasi)
            ->addColumn('tanggal', function ($mutasi) {
                return date('d-m-Y', strtotime($mutasi->created_at));
            })
            ->addColumn('sku', function ($mutasi) {
                return $mutasi->produk->kode_produk;
            })
            ->addColumn('nama', function ($mutasi) {
                return $mutasi->produk->nama_produk;
            })
            ->addColumn('kategori', function ($mutasi) {
                if($mutasi->kategori_mutasi == 'masuk'){
                    return '<span class="badge badge-success">Masuk</span>';
                }else{
                    return '<span class="badge badge-danger">Keluar</span>';
                }
            })
            ->addColumn('jenis', function ($mutasi) {
                return ucwords($mutasi->jenis_mutasi);
            })
            ->addColumn('jumlah', function ($mutasi) {
                return $mutasi->jumlah_stok;
            })
            ->addColumn('keterangan', function ($mutasi) {
                return $mutasi->keterangan;
            })
            ->rawColumns(['kategori'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
