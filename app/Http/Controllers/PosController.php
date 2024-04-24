<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\StokBahan;
use App\Models\ProdukHarga;
use App\Models\ProdukGrosir;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\KeranjangItem;
use App\Models\PenjualanDetail;
use App\Models\SumberPelanggan;
use Yajra\DataTables\Facades\DataTables;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function roundUpTotal($total)
    {
        if($total % 500 != 0) {
            $remainder = $total % 500;
            if($remainder < 250){
                $total = $total - $remainder;
            }else{
                $total = $total + (500 - $remainder);
            }
        }
        return CustomHelper::addCurrencyFormat($total);
    }

    public function addOrder()
    {
        $infoPelanggan = SumberPelanggan::all();
        return view('page.kasir.pos', compact('infoPelanggan'));
    }

    public function daftarPelanggan()
    {
        $id = auth()->user()->sales->id;
        $customer = Customer::where('sales_id', $id)->orWhere('sales_id', 0)->get();
        return response()->json($customer);
    }

    public function manageProduct()
    {
        return view('page.kasir.manage-product');
    }

    public function manageProductJson()
    {
        //Give example 5 data for product with id, kode_produk, nama_produk, price, sell price, stok
        $products = Produk::getProducts();

        return Datatables::of($products)
            ->addIndexColumn()
            ->addColumn('kode_produk', function($row){
                return $row->kode_produk;
            })
            ->addColumn('nama_produk', function($row){
                return $row->nama_produk;
            })
            ->addColumn('harga_kulak', function($row){
                $cabang = auth()->user()->cabang_id;
                if($cabang == 1){
                    $kulak = ProdukHarga::where('produk_id', $row->id)->where('cabang_id', 1)->first();
                }else{
                    $kulak = ProdukHarga::where('produk_id', $row->id)->where('cabang_id', 2)->first();
                }

                if($kulak == null){
                    return 'Belum Diatur';
                }else{
                    return CustomHelper::addCurrencyFormat($kulak->harga_kulak);
                }
            })
            ->addColumn('harga_jual', function($row){
                $cabang = auth()->user()->cabang_id;
                if($cabang == 1){
                    $jual = ProdukHarga::where('produk_id', $row->id)->where('cabang_id', 1)->first();
                }else{
                    $jual = ProdukHarga::where('produk_id', $row->id)->where('cabang_id', 2)->first();
                }

                if($jual == null){
                    return 'Belum Diatur';
                }else{
                    return CustomHelper::addCurrencyFormat($jual->harga_jual);
                }
            })
            ->addColumn('stok_bahan', function($row){
                $cabang = auth()->user()->cabang_id;
                $stok = StokBahan::where('produk_id', $row->id)->where('cabang_id', $cabang)->first();
                if($stok == null){
                    return 'Belum Diatur';
                }
                return $stok->jumlah_stok;
            })
            ->addColumn('action', function($row){
                $actionBtn = '<div class="btn-group">';
                $actionBtn .= '<a href="'.route('pos.editProduct', $row->id).'" class="edit btn btn-warning btn-sm"><i class="fas fa-edit"></i> Ubah</a>';
                $actionBtn .= '<button type="button" class="delete btn btn-danger btn-sm" onclick="hapusProduk('.$row->id.')"><i class="fas fa-trash"></i> Hapus</button>';
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

    public function simpanItem(Request $request, string $id_produk)
    {
        

        $id_keranjang = $request->id_keranjang;
        $produk = Produk::find($id_produk);
        $cabang = auth()->user()->cabang_id;

        function pilihHarga($produk, $cabang){
            if($cabang == 1){
                return $produk->harga_1;
            }else{
                return $produk->harga_2;
            }
        }

        $existingItem = KeranjangItem::where('keranjang_id', $id_keranjang)->where('produk_id', $id_produk)->first();

        if($existingItem){
            $existingItem->jumlah += 1;
            $existingItem->save();
        }else{
            $item = new KeranjangItem;
            $item->keranjang_id = $id_keranjang;
            $item->produk_id = $id_produk;
            $item->jumlah = 1;
            $item->harga = pilihHarga($produk, $cabang);
            $item->diskon = $request->diskon;
            $item->save();
        }

        return response()->json(['success' => 'Produk berhasil ditambahkan ke keranjang']);
    }

    public function hapusItem($id, $cart_id)
    {
        $item = KeranjangItem::find($id);
        $item->delete();

        $totalHarga = KeranjangItem::getItemByIdCart($cart_id);
        $total = 0;

        foreach($totalHarga as $i){
            $subtotal = ($i->harga * $i->jumlah) - $i->diskon;
            $total += $subtotal;
        }

        return response()->json(['success' => 'Produk berhasil dihapus dari keranjang', 'total' => CustomHelper::addCurrencyFormat($total)]);
    }

    public function tambahItem(Request $request)
    {
        $idCart = $request->keranjang_id;
        $id = $request->produk_id;

        if(auth()->user()->cabang_id == 1){
            $hargaProduk = ProdukHarga::where('produk_id', $id)->where('cabang_id', 1)->first()->harga_jual;
        }else{
            $hargaProduk = ProdukHarga::where('produk_id', $id)->where('cabang_id', 2)->first()->harga_jual;
        }

        $existingItem = KeranjangItem::where('keranjang_id', $idCart)->where('produk_id', $id)->first();

        if($existingItem){
            $existingItem->jumlah += 1;
            $existingItem->save();
        }else{
            $item = new KeranjangItem;
            $item->keranjang_id = $idCart;
            $item->produk_id = $id;
            $item->jumlah = 1;
            $item->harga = $hargaProduk;
            $item->diskon = 0;
            $item->save();
        }

        $totalHarga = KeranjangItem::getItemByIdCart($idCart);
        $total = 0;

        foreach($totalHarga as $i){
            $subtotal = ($i->harga * $i->jumlah) - $i->diskon;
            $total += $subtotal;
        }

        return response()->json(['success' => 'Produk berhasil ditambahkan ke keranjang', 'total' => CustomHelper::addCurrencyFormat($total)]);
    }

    public function createProduct()
    {
        return view('page.kasir.tambah-produk');
    }

    public function simpanProduk(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk',
            'nama_produk' => 'required|max:255',
            'harga_kulak' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer'
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
        $validated = $request->validate([
            'id_produk' => 'required',
            'kode_produk' => 'required|unique:produk,kode_produk',
            'nama_produk' => 'required|max:255',
            'harga_kulak' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer'
        ]);

        $cabang_id = auth()->user()->cabang_id;
        $idProduk = $validated['id_produk'];

        $produk = Produk::find($idProduk);
        $produk->kode_produk = $validated['kode_produk'];
        $produk->nama_produk = ucwords(strtolower($validated['nama_produk']));
        $produk->save();

        $harga = ProdukHarga::where('produk_id', $idProduk)->where('cabang_id', $cabang_id)->first();
        $harga->harga_kulak = $validated['harga_kulak'];
        $harga->harga_jual = $validated['harga_jual'];
        $harga->save();

        $stok = StokBahan::where('produk_id', $idProduk)->where('cabang_id', $cabang_id)->first();
        $stok->jumlah_stok = $validated['stok'];
        $stok->save();

        if(isset($request->min) && isset($request->max)){
            //perulangan untuk menambahkan harga grosir
            for($i = 0; $i < count($request->min); $i++){
                $grosir = ProdukGrosir::where('produk_id', $idProduk)->where('cabang_id', $cabang_id)->get();
                $grosir[$i]->min_qty = $request->min[$i];
                $grosir[$i]->max_qty = $request->max[$i];
                $grosir[$i]->harga_grosir = $request->harga[$i];
                $grosir[$i]->save();
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

    public function destroyProduct(string $id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return redirect()->route('pos.manageProduct')->with('success', 'Produk berhasil dihapus!');
    }

    //--------------------------------------------------------------------------------------------
    //Fungsi untuk Keranjang
    //--------------------------------------------------------------------------------------------
    public function setupKeranjang(Request $request)
    {
        $cabang = auth()->user()->cabang_id;
        $sales = auth()->user()->sales->id;
        $user = auth()->user()->id;
        $customer = $request->customer_id;

        $existingItem = Keranjang::where('sales_id', $sales)->where('customer_id', $customer)->first();

        if($existingItem){
            $totalItem = KeranjangItem::getItemByIdCart($existingItem->id);

            $totalHarga = 0;

            foreach($totalItem as $item){
                $subtotal = ($item->harga * $item->jumlah) - $item->diskon;
                $totalHarga += $subtotal;
            }

            return response()->json(['success' => 'Keranjang sudah ada', 'total' => CustomHelper::addCurrencyFormat($totalHarga), 'id' => $existingItem->id]);
        }else{
            $keranjang = new Keranjang;
            $keranjang->cabang_id = $cabang;
            $keranjang->sales_id = $sales;
            $keranjang->user_id = $user;
            $keranjang->customer_id = $customer;
            $keranjang->save();

            $totalItem = KeranjangItem::getTotalItem($keranjang->id);
            if($totalItem == 0 || $totalItem == null){
                $totalItem = 0;
            }

            return response()->json(['success' => 'Keranjang berhasil dibuat', 'total' => $totalItem, 'id' => $keranjang->id]);
        }
    }

    public function tambahKeranjang(Request $request)
    {
        $keranjang = new Keranjang;
        $keranjang->cabang_id = $request->cabang_id;
        $keranjang->sales_id = $request->sales_id;
        $keranjang->user_id = $request->user_id;
        $keranjang->save();

        $produk = Produk::find($request->produk_id);

        $item = new KeranjangItem;
        $item->keranjang_id = $keranjang->id;
        $item->produk_id = $request->produk_id;
        $item->jumlah = 1;
        if($request->cabang_id == 1){
            $item->harga = $produk->harga_1;
        }else{
            $item->harga = $produk->harga_2;
        }
        $item->harga = $produk->harga;
        $item->diskon = $request->diskon;
        $item->save();

        return response()->json(['success' => 'Produk berhasil ditambahkan ke keranjang']);
    }

    public function isGrosir($produkId, $qty)
    {
        $cabangId = auth()->user()->cabang_id;
        if($cabangId != 1){
            $cabangId = 2;
        }

        $grosir = ProdukGrosir::where('produk_id', $produkId)->where('cabang_id', $cabangId)->get();
        if($grosir->count() > 0){
            foreach($grosir as $g){
                if($qty >= $g->min_qty && $qty <= $g->max_qty){
                    return true;
                }
            }
        }else{
            return false;
        }
    }

    public function tampilkanKeranjang(string $id_cart)
    {
        $items = KeranjangItem::getItemByIdCart($id_cart);

        return Datatables::of($items)
            ->addColumn('nama_produk', function($row){
                //jika isGrosir true, maka muncul badge grosir
                if($this->isGrosir($row->produk_id, $row->jumlah)){
                    return $row->produk->nama_produk.' <span class="badge badge-primary">Grosir</span>';
                }else{
                    return $row->produk->nama_produk;
                }
            })
            ->addColumn('harga', function($row){
                return CustomHelper::addCurrencyFormat($row->harga);
            })
            ->addColumn('diskon', function($row){
                $diskon = CustomHelper::addCurrencyFormat($row->diskon);
                return '<input id="diskon" type="text" style="width: 80px;" class="form-control maskMoney" value="'.$row->diskon.'" onchange="updateDiskon('.$row->id.', this.value)">';
            })
            ->addColumn('total', function($row){
                $total = ($row->harga * $row->jumlah) - $row->diskon;
                return CustomHelper::addCurrencyFormat($total);
            })
            ->addColumn('qty', function($row){
                return '<input id="qty" type="number" style="width: 80px;" class="form-control" value="'.$row->jumlah.'" onchange="updateQty('.$row->id.', this.value)">';
            })
            ->addColumn('action', function($row){
                $actionBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="hapusItem('.$row->id.')"><i class="fas fa-trash"></i></button>';
                return $actionBtn;
            })
            ->rawColumns(['nama_produk', 'action', 'qty', 'diskon', 'total'])
            ->make(true);
    }

    public function updateQty(Request $request)
    {
        $item = KeranjangItem::find($request->id);
        $item->jumlah = $request->qty;
        $item->save();

        $items = KeranjangItem::getItemByIdCart($item->keranjang_id);
        $total = 0;

        foreach($items as $i){
            $cabang = auth()->user()->cabang_id;
            if($cabang != 1){
                $cabang = 2;
            }
            //jika isGrosir true, maka harga grosir dikalikan jumlah
            $grosir = ProdukGrosir::where('produk_id', $i->produk_id)->where('cabang_id', $cabang)->where('min_qty', '<=', $i->jumlah)->where('max_qty', '>=', $i->jumlah)->first();
            if($grosir != null || isset($grosir)){
                    $hargaGrosir = $grosir->harga_grosir;
                    $i->harga = $hargaGrosir; // update price with harga_grosir
                    $i->save();
                }else{
                    //jika tidak ada harga grosir, maka harga produk tetap menggunakan pada tabel produk_harga
                    $hargaGrosir = ProdukHarga::where('produk_id', $i->produk_id)->where('cabang_id', $cabang)->first()->harga_jual;
                    $i->harga = $hargaGrosir; // update price with harga_produk
                    $i->save();
                }
            $total += ($hargaGrosir * $i->jumlah) - $i->diskon;
        }

        return response()->json(['total' => CustomHelper::addCurrencyFormat($total)]);
    }

    public function updateDiskon(Request $request)
    {
        $item = KeranjangItem::find($request->id);
        $item->diskon = $request->diskon;
        $item->save();

        $items = KeranjangItem::getItemByIdCart($item->keranjang_id);
        $total = 0;

        foreach($items as $i){
            $subtotal = ($i->harga * $i->jumlah) - $i->diskon;
            $total += $subtotal;
        }

        return response()->json(['success' => 'Diskon produk berhasil diubah', 'total' => CustomHelper::addCurrencyFormat($total)]);
    }

    public function checkoutCart(string $cart_id)
    {
        //cek apakah ada keranjang dengan id tersebut
        $keranjang = Keranjang::find($cart_id);
        if($keranjang == null){
            return redirect()->route('pos.addOrder')->with('error', 'Keranjang tidak ditemukan');
        }

        $items = KeranjangItem::getItemByIdCart($cart_id);
        $total = 0;
        $diskon = 0;
        $cart_id = $cart_id;

        $customer_id = Keranjang::find($cart_id)->customer_id;

        $nama_customer = Customer::find($customer_id)->nama;

        //perulangan untuk menghitung total harga
        foreach($items as $item){
            //jika isGrosir true, maka harga grosir dikalikan jumlah
            $total += ($item->harga * $item->jumlah) - $item->diskon;
            $diskon += $item->diskon;
        }

        $pembulatan = $this->roundUpTotal($total);
        $total = CustomHelper::addCurrencyFormat($total);
        $diskon = CustomHelper::addCurrencyFormat($diskon);

        return view('page.kasir.checkout', compact('items', 'total', 'diskon', 'cart_id', 'pembulatan', 'customer_id', 'nama_customer'));
    }

    public function checkoutCartJson(string $cart_id)
    {
        $items = KeranjangItem::getItemByIdCart($cart_id);

        return Datatables::of($items)
            ->addColumn('nama_produk', function($row){
                //jika isGrosir true, maka muncul badge grosir
                if($this->isGrosir($row->produk_id, $row->jumlah)){
                    return $row->produk->nama_produk.' <span class="badge badge-primary">Grosir</span>';
                }else{
                    return $row->produk->nama_produk;
                }
            })
            ->addColumn('harga', function($row){
                return CustomHelper::addCurrencyFormat($row->harga);
            })
            ->addColumn('diskon', function($row){
                $diskon = CustomHelper::addCurrencyFormat($row->diskon);
                return $diskon;
            })
            ->addColumn('total', function($row){
                $total = ($row->harga * $row->jumlah) - $row->diskon;
                return CustomHelper::addCurrencyFormat($total);
            })
            ->addColumn('qty', function($row){
                return $row->jumlah;
            })
            ->addColumn('action', function($row){
                $actionBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="hapusItem('.$row->id.')"><i class="fas fa-trash"></i></button>';
                return $actionBtn;
            })
            ->rawColumns(['nama_produk', 'action', 'qty', 'diskon', 'total'])
            ->make(true);
    }

    public function buatPesanan(Request $request)
    {

        $cabang = auth()->user()->cabang_id;
        $sales = auth()->user()->sales->id;
        $customer = $request->customer_id;
        $date = date('ym');

        $keranjang = Keranjang::where('sales_id', $sales)->where('customer_id', $customer)->first();
        $items = KeranjangItem::getItemByIdCart($keranjang->id);

        //get latest id from penjualan
        $latest = Penjualan::latest()->first();
        if($latest == null){
            $latestId = 1;
        }else{
            $latestId = $latest->id + 1;
        }
        //generate invoice
        $invoice = 'INV/'. $cabang . $sales.'/'.$customer.'/'.$date.'/'.$latestId;
        //sum diskon
        $diskon = 0;
        foreach($items as $item){
            $diskon += $item->diskon;
        }

        //create new penjualan
        $penjualan = new Penjualan;
        $penjualan->customer_id = $customer;
        $penjualan->sales_id = $sales;
        $penjualan->no_invoice = $invoice;
        $penjualan->total = $request->total;
        $penjualan->diskon = $diskon;
        $penjualan->diterima = CustomHelper::removeCurrencyFormat($request->total_bayar);
        $penjualan->keterangan = $request->keterangan;
        $penjualan->metode_pembayaran = $request->metode;
        $penjualan->rekening = $request->rekening == null || $request->rekening == "" ? null : $request->rekening;
        $penjualan->alamat = $request->alamat == null || $request->alamat == "" ? null : $request->alamat;
        $penjualan->telepon = $request->telepon == null || $request->telepon == "" ? null : $request->telepon;
        $penjualan->status = 1;
        $penjualan->save();

        if($penjualan->save()){
            //create new penjualan detail (item penjualan)
            foreach($items as $item){
                $detail = new PenjualanDetail;
                $detail->penjualan_id = $penjualan->id;
                $detail->produk_id = $item->produk_id;
                $detail->harga = $item->harga;
                $detail->jumlah = $item->jumlah;
                $detail->diskon = $item->diskon;
                $detail->save();
            }

            //hapus item keranjang
            foreach($items as $item){
                $item->delete();
            }
        }

        //delete keranjang
        $keranjang->delete();

        return response()->json(['success' => 'Pesanan berhasil dibuat !']);
    }

    public function tampilFaktur(string $id)
    {
        $penjualan = Penjualan::find($id);
        $items = PenjualanDetail::where('penjualan_id', $id)->get();
        $salesId = auth()->user()->sales->id;
        $sales = Sales::find($salesId);

        $diskon = 0;
        $total = 0;
        foreach($items as $item){
            $diskon += $item->diskon;
            $subtotal = ($item->harga * $item->jumlah) - $item->diskon;
            $total += $subtotal;
        }

        $rekening = $penjualan->rekening;
        if($rekening == 'tunai'){
            $rekening = 'Tunai';
        }elseif($rekening == 'bca'){
            $rekening = 'Bank BCA';
        }elseif($rekening == 'bni'){
            $rekening = 'Bank BNI';
        }elseif($rekening == 'bri'){
            $rekening = 'Bank BRI';
        }elseif($rekening == 'mandiri'){
            $rekening = 'Bank Mandiri';
        }else{
            $rekening = 'Lainnya';
        }

        return view('page.kasir.invoice-penjualan', compact('penjualan', 'items', 'sales', 'diskon', 'total', 'rekening'));
    }

    public function printFaktur(string $id)
    {
        $penjualan = Penjualan::find($id);
        $items = PenjualanDetail::where('penjualan_id', $id)->get();
        $salesId = auth()->user()->sales->id;
        $sales = Sales::find($salesId);

        $diskon = 0;
        $total = 0;
        foreach($items as $item){
            $diskon += $item->diskon;
            $subtotal = ($item->harga * $item->jumlah) - $item->diskon;
            $total += $subtotal;
        }

        $rekening = $penjualan->rekening;
        if($rekening == 'tunai'){
            $rekening = 'Tunai';
        }elseif($rekening == 'bca'){
            $rekening = 'Bank BCA';
        }elseif($rekening == 'bni'){
            $rekening = 'Bank BNI';
        }elseif($rekening == 'bri'){
            $rekening = 'Bank BRI';
        }elseif($rekening == 'mandiri'){
            $rekening = 'Bank Mandiri';
        }else{
            $rekening = 'Lainnya';
        }

        return view('page.kasir.invoice-print', compact('penjualan', 'items', 'sales', 'diskon', 'total', 'rekening'));
    }

    public function penjualanToday(string $bulan)
    {
        $sales = auth()->user()->sales->id;
        $penjualan = Penjualan::where('sales_id', $sales)->whereDate('created_at', date('Y-'.$bulan.'-d'))->get();

        $omsetToday = 0;
        foreach($penjualan as $p){
            $omsetToday += $p->total;
        }

        $omsetToday = CustomHelper::addCurrencyFormat($omsetToday);

        return response()->json($omsetToday);
    }

    public function penjualanBulanan($bulan)
    {
        $sales = auth()->user()->sales->id;
        $penjualan = Penjualan::where('sales_id', $sales)->whereMonth('created_at', $bulan)->get();

        $omsetMonth = 0;
        foreach($penjualan as $p){
            $omsetMonth += $p->total;
        }

        $omsetMonth = CustomHelper::addCurrencyFormat($omsetMonth);

        return response()->json($omsetMonth);
    }

    public function laporanBahan(Request $request)
    {
        $sales = auth()->user()->sales->id;
        $bulan = $request->query('bulan') ?? date('m');
        if(!isset($bulan)){
            $awal = date('Y-m-01');
            $akhir = date('Y-m-31');
        }else{
            $awal = date('Y-'.$bulan.'-01');
            $akhir = date('Y-'.$bulan.'-t');
        }

        return view('page.kasir.penjualan', compact('bulan'));
    }

    public function laporanBahanJson(Request $request)
    {
        $sales = auth()->user()->sales->id;
        $filter = $request->query('bulan') ?? date('m');
        if(!isset($filter)){
            $awal = date('Y-m-01');
            $akhir = date('Y-m-t');
        }else{
            $awal = date('Y-'.$filter.'-01');
            $akhir = date('Y-'.$filter.'-t');
        }

        $penjualan = Penjualan::where('sales_id', $sales)->whereBetween('created_at', [$awal, $akhir])->get();

        return Datatables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('no_invoice', function($row){
                return $row->no_invoice;
            })
            ->addColumn('tanggal', function($row){
                return date_format($row->created_at, 'd F Y');
            })
            ->addColumn('customer', function($row){
                return $row->customer->nama;
            })
            ->addColumn('total', function($row){
                return CustomHelper::addCurrencyFormat($row->total);
            })
            ->addColumn('action', function($row){
                $actionBtn = '<div class="btn-group">';
                $actionBtn .= '<a href="'.route('pos.faktur', $row->id).'" class="btn btn-primary btn-sm"><i class="fas fa-list-alt"></i> Invoice</a>';
                $actionBtn .= '<a href="#" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Print Nota</a>';
                $actionBtn .= '</div>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function laporanItem()
    {
        $sales = auth()->user()->sales->id;
        $awal = date('Y-m-01');
        $akhir = date('Y-m-t');
        $bulan = date('m');

        $penjualanDetail = PenjualanDetail::whereHas('penjualan', function($q) use($sales){
            $q->where('sales_id', $sales);
        })->whereBetween('created_at', [$awal, $akhir])->get();

        $laba = 0;
        foreach($penjualanDetail as $p){
            $cabang = auth()->user()->cabang_id;
            $harga = ProdukHarga::where('produk_id', $p->produk_id)->where('cabang_id', $cabang)->first();
            $laba += ($p->harga - $harga->harga_kulak) * $p->jumlah;
        }
        $laba = CustomHelper::addCurrencyFormat($laba);

        $total = 0;
        foreach($penjualanDetail as $p){
            $subtotal = ($p->harga * $p->jumlah) - $p->diskon;
            $total += $subtotal;
        }
        $total = CustomHelper::addCurrencyFormat($total);

        return view('page.kasir.penjualan-item', compact('penjualanDetail', 'laba', 'total', 'bulan'));
    }

    public function itemsJson()
    {
        $sales = auth()->user()->sales->id;
        $awal = date('Y-m-01');
        $akhir = date('Y-m-t');

        $penjualanDetail = PenjualanDetail::whereHas('penjualan', function($q) use($sales){
            $q->where('sales_id', $sales);
        })->whereBetween('created_at', [$awal, $akhir])->get();

        return Datatables::of($penjualanDetail)
            ->addIndexColumn()
            ->addColumn('tanggal', function($row){
                return date_format($row->penjualan->created_at, 'd F Y');
            })
            ->addColumn('produk', function($row){
                return $row->produk->nama_produk;
            })
            ->addColumn('jumlah', function($row){
                return $row->jumlah;
            })
            ->addColumn('kulak', function($row){
                $cabang = auth()->user()->cabang_id;
                $harga = ProdukHarga::where('produk_id', $row->produk_id)->where('cabang_id', $cabang)->first();
                return CustomHelper::addCurrencyFormat($harga->harga_kulak);
            })
            ->addColumn('harga', function($row){
                return CustomHelper::addCurrencyFormat($row->harga);
            })
            ->addColumn('total', function($row){
                $total = ($row->harga * $row->jumlah) - $row->diskon;
                return CustomHelper::addCurrencyFormat($total);
            })
            ->addColumn('laba', function($row){
                $cabang = auth()->user()->cabang_id;
                $harga = ProdukHarga::where('produk_id', $row->produk_id)->where('cabang_id', $cabang)->first();
                $laba = ($row->harga - $harga->harga_kulak) * $row->jumlah;
                return CustomHelper::addCurrencyFormat($laba);
            })
            
            ->rawColumns(['tanggal', 'produk', 'jumlah', 'kulak', 'harga', 'total', 'laba'])
            ->make(true);
    }

    public function penjualanItemBulanan($bulan)
    {
        $items = PenjualanDetail::whereHas('penjualan', function($q){
            $q->where('sales_id', auth()->user()->sales->id);
        })->whereMonth('created_at', $bulan)->get();

        $total = 0;

        foreach($items as $item){
            $subtotal = ($item->harga * $item->jumlah) - $item->diskon;
            $total += $subtotal;
        }

        return response()->json(CustomHelper::addCurrencyFormat($total));
    }

    public function labaBulanan($bulan)
    {
        $items = PenjualanDetail::whereHas('penjualan', function($q){
            $q->where('sales_id', auth()->user()->sales->id);
        })->whereMonth('created_at', $bulan)->get();

        $laba = 0;

        foreach($items as $item){
            $cabang = auth()->user()->cabang_id;
            $harga = ProdukHarga::where('produk_id', $item->produk_id)->where('cabang_id', $cabang)->first();
            $laba += ($item->harga - $harga->harga_kulak) * $item->jumlah;
        }

        return response()->json(CustomHelper::addCurrencyFormat($laba));
    }
}
