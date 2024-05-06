<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokBahan;

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

    public function mutasiStok()
    {
        return view('page.stok.mutasi-stok');
    }

    public function daftarStokJson()
    {
        function getStok($id)
        {
            $cabang = auth()->user()->cabang_id;
            $stok = StokBahan::where('produk_id', $id)->where('cabang_id', $cabang)->first()->jumlah_stok;
            return $stok;
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
                return getStok($stock->id);
            })
            ->addColumn('terjual', function ($stock) {
                return getStok($stock->id);
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
