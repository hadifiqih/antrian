<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Produk;
use App\Models\StokBahan;
use App\Models\ProdukHarga;
use App\Models\ProdukGrosir;

use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;

use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function manageProduct()
    {
        return view('page.kasir.manage-product');
    }

    public function showProduct($id)
    {
        $produk = Produk::find($id);

        return view('page.kasir.detail-produk', compact('produk'));
    }

    public function manageProductJson()
    {
        // Ambil semua produk
        $products = Produk::getProducts();

        // Ambil semua data ProdukHarga berdasarkan cabang user yang sedang login.
        $cabangId = auth()->user()->cabang_id;
        $produkHarga = ProdukHarga::where('cabang_id', $cabangId)->get();

        // Ambil semua data StokBahan berdasarkan cabang user yang sedang login.
        $stokBahan = StokBahan::where('cabang_id', $cabangId)->get();

        // Ubah data ProdukHarga dan StokBahan menjadi bentuk array 
        // dengan key produk_id agar mudah diakses.
        $hargaKulak = $produkHarga->keyBy('produk_id');
        $hargaJual = $produkHarga->keyBy('produk_id');
        $stokBahan = $stokBahan->keyBy('produk_id');

        return Datatables::of($products)
            ->addIndexColumn()
            ->addColumn('kode_produk', function($row){
                return $row->kode_produk;
            })
            ->addColumn('nama_produk', function($row){
                return $row->nama_produk;
            })
            ->addColumn('harga_kulak', function($row) use ($hargaKulak) {
                // Cek apakah data harga_kulak ada untuk produk ini.
                if(isset($hargaKulak[$row->id])){
                    return CustomHelper::addCurrencyFormat($hargaKulak[$row->id]->harga_kulak);
                }else{
                    return 'Belum Diatur';
                }
            })
            ->addColumn('harga_jual', function($row) use ($hargaJual) {
                // Cek apakah data harga_jual ada untuk produk ini.
                if(isset($hargaJual[$row->id])){
                    return CustomHelper::addCurrencyFormat($hargaJual[$row->id]->harga_jual);
                }else{
                    return 'Belum Diatur';
                }
            })
            ->addColumn('stok_bahan', function($row) use ($stokBahan) {
                // Cek apakah data stok_bahan ada untuk produk ini.
                if(isset($stokBahan[$row->id])){
                    return $stokBahan[$row->id]->jumlah_stok;
                }else{
                    return 'Belum Diatur';
                }
            })
            ->addColumn('action', function($row){
                $actionBtn = '<div class="btn-group">';
                $actionBtn .= '<a href="'.route('pos.editProduct', $row->id).'" class="edit btn btn-warning btn-sm"><i class="fas fa-edit"></i> Ubah</a>';
                $actionBtn .= '<button type="button" class="delete btn btn-danger btn-sm" onclick="hapusProduk('.$row->id.')"><i class="fas fa-trash"></i> Hapus</button>';
                $actionBtn .= '<a href="'.route('pos.showProduct', $row->id).'" class="show btn btn-primary btn-sm"><i class="fas fa-eye"></i> Detail</a>';
                $actionBtn .= '</div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'stok_bahan'])
            ->make(true);
    }

    public function pilihProduk()
    {
        $produk = Produk::getProducts();

        return Datatables::of($produk)
            ->addIndexColumn()
            ->addColumn('harga_jual', function($row){
                $cabang = auth()->user()->cabang_id;
                $harga = ProdukHarga::where('produk_id', $row->id)->where('cabang_id', $cabang)->first();
                if($harga == null){
                    return 'Belum Diatur';
                }
                return CustomHelper::addCurrencyFormat($harga->harga_jual);
            })
            ->addColumn('stok', function($row){;
                $cabang = auth()->user()->cabang_id;
                $stok = StokBahan::where('produk_id', $row->id)->where('cabang_id', $cabang)->first();
                if($stok == null){
                    return 'Belum Diatur';
                }
                return $stok->jumlah_stok;
            })
            ->addColumn('action', function($row){
                $actionBtn = '<button type="button" class="btn btn-primary btn-sm" onclick="tambahItem('.$row->id.')">Tambah</button>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createProduct()
    {
        return view('page.kasir.tambah-produk');
    }

    public function simpanProduk(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|unique:produk',
            'nama_produk' => 'required|max:255',
            'harga_kulak' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer'
        ], [
            'kode_produk.unique' => 'Kode produk sudah digunakan'
        ]);

        $cabang_id = auth()->user()->cabang_id;

        $produk = new Produk;
        $produk->kode_produk = $validated['kode_produk'];
        $produk->nama_produk = ucwords(strtolower($validated['nama_produk']));
        $produk->save();

        $harga = new ProdukHarga;
        $harga->produk_id = $produk->id;
        $harga->cabang_id = $cabang_id;
        $harga->harga_kulak = $validated['harga_kulak'];
        $harga->harga_jual = $validated['harga_jual'];
        $harga->save();

        $stok = new StokBahan;
        $stok->produk_id = $produk->id;
        $stok->cabang_id = $cabang_id;
        $stok->jumlah_stok = $validated['stok'];
        $stok->save();

        if(isset($request->min) && isset($request->max)){
            //perulangan untuk menambahkan harga grosir
            for($i = 0; $i < count($request->min); $i++){
                $grosir = new ProdukGrosir;
                $grosir->produk_id = $produk->id;
                $grosir->cabang_id = $cabang_id;
                $grosir->min_qty = $request->min[$i];
                $grosir->max_qty = $request->max[$i];
                $grosir->harga_grosir = $request->harga[$i];
                $grosir->save();
            }
        }

        if($produk->save() && $harga->save() && $stok->save() && $grosir->save()){
            return redirect()->route('pos.manageProduct')->with('success', 'Produk berhasil ditambahkan');
        }elseif($produk->save() && $harga->save() && $stok->save()){
            return redirect()->route('pos.manageProduct')->with('success', 'Produk berhasil ditambahkan');
        }else{
            return redirect()->route('pos.manageProduct')->with('error', 'Produk gagal ditambahkan');
        }
    }

    public function editProduct(string $id)
    {
        $produk = Produk::getProduct($id);
        $cabang = auth()->user()->cabang_id;
        $harga = ProdukHarga::where('produk_id', $id)->where('cabang_id', $cabang)->first();
        $grosir = ProdukGrosir::where('produk_id', $id)->where('cabang_id', $cabang)->get();
        $stok = StokBahan::where('produk_id', $id)->where('cabang_id', $cabang)->first();

        return view('page.kasir.ubah-produk', compact('produk', 'cabang', 'harga', 'grosir', 'stok'));
    }

    public function updateProduct(Request $request, string $id)
    {
        // Validasi input dengan pesan error yang lebih deskriptif
        $validated = $request->validate([
            'id_produk' => 'required',
            'kode_produk' => 'required',
            'nama_produk' => 'required|max:255',
            'harga_kulak' => 'required',
            'harga_jual' => 'required',
            'stok' => 'required',
            'min.*' => 'nullable', 
            'max.*' => 'nullable', 
            'harga.*' => 'nullable', 
        ]);

        $cabang = auth()->user()->cabang_id;
        $idProduk = $validated['id_produk'];

        // Temukan model hanya sekali di awal
        $produk = Produk::findOrFail($idProduk);
        $harga = ProdukHarga::where('produk_id', $idProduk)->where('cabang_id', $cabang)->first();
        $stok = StokBahan::where('produk_id', $idProduk)->where('cabang_id', $cabang)->first();

        // Assign value ke property model
        $produk->nama_produk = ucwords(strtolower($validated['nama_produk']));

        if(!$harga){
            $harga = new ProdukHarga;
            $harga->produk_id = $idProduk;
            $harga->cabang_id = $cabang;
        }

        $harga->harga_kulak = CustomHelper::removeCurrencyFormat($validated['harga_kulak']);
        $harga->harga_jual = CustomHelper::removeCurrencyFormat($validated['harga_jual']);
        
        if(!$stok){
            $stok = new StokBahan;
            $stok->produk_id = $idProduk;
            $stok->cabang_id = $cabang;
        }

        $stok->jumlah_stok = $validated['stok'];

        // Gunakan transaction untuk memastikan konsistensi data
        DB::transaction(function () use ($produk, $harga, $stok, $request, $idProduk, $cabang) {
            $produk->save();
            $harga->save();
            $stok->save();

            // Hapus semua data grosir sebelumnya
            ProdukGrosir::where('produk_id', $idProduk)->where('cabang_id', $cabang)->delete();

            // Tambahkan data grosir baru
            if (isset($request->min) && isset($request->max) && isset($request->harga)) {
                foreach ($request->min as $index => $minQty) {
                    ProdukGrosir::create([
                        'produk_id' => $idProduk,
                        'cabang_id' => $cabang,
                        'min_qty' => $minQty,
                        'max_qty' => $request->max[$index],
                        'harga_grosir' => $request->harga[$index],
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Produk berhasil diperbarui!'], 200);
    }

    public function destroyProduct(string $id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return redirect()->route('pos.manageProduct')->with('success', 'Produk berhasil dihapus!');
    }
}
