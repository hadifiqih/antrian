<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Cabang;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\StokBahan;
use App\Models\MutasiStok;
use Mike42\Escpos\Printer;
use App\Models\ProdukHarga;
use App\Models\ProdukGrosir;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\KeranjangItem;

use App\Models\PenjualanDetail;
use App\Models\SumberPelanggan;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NotaResource;
use Illuminate\Support\Facades\Http;
use Mike42\Escpos\CapabilityProfile;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class PosController extends Controller
{
    //constructor
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function notaStruk($id)
    {
        // //ambil data dari api
        // $response = Http::get('http://dashboard.kassabsyariah.com/api/pos/nota/'.$id);
        $penjualan = Penjualan::find($id);
        $sales = Sales::find();
        $items = $response['data']['items'];

        $connector = new WindowsPrintConnector("POS-80");
        $printer = new Printer($connector);

        //print nota order dengan menggunakan printer thermal
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("STRUK PEMBELIAN\n");
        $printer->setEmphasis(true);
        $printer->text($sales->sales_name."\n");
        $printer->setEmphasis(false);
        $printer->text($sales->address."\n");
        $printer->text("WA. ".$sales->sales_phone."\n");
        $printer->text("===============================================\n");
        $printer->setEmphasis(true);
        $printer->text($penjualan->no_invoice . "\n");
        $printer->setEmphasis(false);
        $printer->text("===============================================\n");
        $printer->text("\n");
        $printer->text(buatBaris2Kolom(date_format($penjualan->created_at ,'d F Y') , date_format($penjualan->created_at ,'H:i')));
        $printer->text(buatBaris2Kolom('Pelanggan', $penjualan->customer->nama));
        $printer->text(buatBaris2Kolom('Kasir', $sales->sales_name));
        $printer->text("-----------------------------------------------\n");

        foreach($items as $item){
            $qty = $item->jumlah . 'x';
            $harga = '@'. number_format($item->harga, 0, ',', '.');
            $printer->setEmphasis(true);
            $printer->text(buatBaris1Kolom($item->produk->nama_produk));
            $printer->setEmphasis(false);
            $printer->text(buatBaris3Kolom($qty, $harga , CustomHelper::addCurrencyFormat($item->harga * $item->jumlah)));
        }

        $printer->text("-----------------------------------------------\n");
        $printer->text(buatBaris2Kolom('Subtotal', CustomHelper::addCurrencyFormat($penjualan->total)));
        $printer->text(buatBaris2Kolom('Diskon', CustomHelper::addCurrencyFormat($penjualan->diskon)));
        $printer->text("-----------------------------------------------\n");
        $printer->text(buatBaris2Kolom('Cash/Transfer', CustomHelper::addCurrencyFormat($penjualan->diterima)));
        $printer->text(buatBaris2Kolom('Kembali', CustomHelper::addCurrencyFormat($penjualan->diterima - ($penjualan->total - $penjualan->diskon))));
        $printer->text("-----------------------------------------------\n");
        $printer->text("-- Terima kasih --\n");
        $printer->text("Selamat Datang Kembali\n");

        $printer->cut();
        $printer->close();
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
                $stok = StokBahan::where('produk_id', $row->produk_id)->where('cabang_id', auth()->user()->cabang_id)->first()->jumlah_stok;
                return '<input id="qty" type="number" style="width: 80px;" class="form-control" value="'.$row->jumlah.'" onchange="updateQty('.$row->id.', this.value)"><p class="text-sm text-danger font-weight-bold">Max. '. $stok .' pcs</p>';
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

        try{
            $item->jumlah = $request->qty;
            $item->save();
        }catch(\Exception $e){
            return response()->json(['message' => 'Gagal mengubah jumlah produk', 'error' => $e->getMessage], 500);
        }

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

        $cabangs = Cabang::all();

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

        return view('page.kasir.checkout', compact('cabangs','items', 'total', 'diskon', 'cart_id', 'pembulatan', 'customer_id', 'nama_customer'));
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
        $cabang = $request->dikirimDari;
        $sales = auth()->user()->sales->id;
        $customer = $request->customer_id;
        $date = date('ym');
        $month = date('m');

        $keranjang = Keranjang::where('sales_id', $sales)->where('customer_id', $customer)->first();
        $items = KeranjangItem::getItemByIdCart($keranjang->id);

        //get latest id from penjualan
        $latest = Penjualan::whereMonth('created_at', $month)->count();

        if($latest == 0){
            $latestId = 1;
        }else{
            $latestId = $latest + 1;
        }

        //generate invoice
        $invoice = 'INV/'. $cabang . $sales . $date . $latestId;

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
        $penjualan->cabang_id = $cabang;
        $penjualan->metode_pembayaran = $request->metode;
        $penjualan->ppn = 0; //ppn 11%
        $penjualan->pph = 0; //pph 2,5%
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

                //update stok bahan
                $stok = StokBahan::where('produk_id', $item->produk_id)->where('cabang_id', $cabang)->first();
                $stok->jumlah_stok -= $item->jumlah;
                $stok->save();

                //buat mutasi stok
                $mutasi = new MutasiStok;
                $mutasi->produk_id = $item->produk_id;
                $mutasi->cabang_id = $cabang;
                $mutasi->kategori_mutasi = 'keluar';
                $mutasi->jenis_mutasi = 'penjualan';
                $mutasi->jumlah_stok = $item->jumlah;
                $mutasi->keterangan = 'Penjualan : '. $invoice;
                $mutasi->save();
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
        $penjualan = Penjualan::with(['customer'])->find($id);
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

    public function updatePenjualanBahan(string $bulan)
    {
        $tahun = date('Y');
        $today = date('Y-m-d');
        $query = Penjualan::query();

        $awalBulan = date("$tahun-$bulan-01");
        $akhirBulan = date("$tahun-$bulan-t"); // 't' akan memberikan hari terakhir dari bulan

        if (auth()->user()->role_id == 11) {
            $sales = auth()->user()->sales->id;
            $query->where('sales_id', $sales);
        }

        $penjualanHarian = (clone $query)->whereDate('created_at', $today)->sum('total');
        $penjualanBulanan = (clone $query)->whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total');

        return response()->json([
            'today' => CustomHelper::addCurrencyFormat($penjualanHarian),
            'monthly' => CustomHelper::addCurrencyFormat($penjualanBulanan)
        ]);
    }

    public function laporanBahan(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = date('Y');

        $awal = date("$tahun-$bulan-01");
        $akhir = date("$tahun-$bulan-t");

        return view('page.kasir.penjualan', compact('bulan', 'awal', 'akhir'));
    }

    public function laporanBahanJson(Request $request)
    {
        $filter = $request->query('bulan', date('m'));
        if(!isset($filter)){
            $awal = date('Y-m-01');
            $akhir = date('Y-m-t');
        }else{
            $awal = date('Y-'.$filter.'-01');
            $akhir = date('Y-'.$filter.'-t');
        }

        if(auth()->user()->role_id == 11){
            $sales = auth()->user()->sales->id;
            $penjualan = Penjualan::with(['customer'])->where('sales_id', $sales)->whereBetween('created_at', [$awal, $akhir])->get();
        }else{
            $penjualan = Penjualan::with(['customer'])->whereBetween('created_at', [$awal, $akhir])->get();
        }
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
                $actionBtn .= '<a href="'.route('pos.detailTransaksi', $row->id).'" class="btn btn-primary btn-sm"><i class="fas fa-list-alt"></i> Invoice</a>';
                $actionBtn .= '<a href="cetakstrukantree://retail" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Print Nota</a>';
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

    public function itemsJson(Request $request)
    {
        $sales = auth()->user()->sales->id;
        $cabang = auth()->user()->cabang_id;
        $filter = $request->query('bulan') ?? date('m');
        if(!isset($filter)){
            $awal = date('Y-m-01');
            $akhir = date('Y-m-t');
        }else{
            $awal = date('Y-'.$filter.'-01');
            $akhir = date('Y-'.$filter.'-t');
        }

        $penjualanDetail = PenjualanDetail::with(['penjualan', 'produk'])
        ->whereHas('penjualan', function($q) use($sales){
            $q->where('sales_id', $sales);
        })->whereBetween('created_at', [$awal, $akhir])
        ->get();

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

    public function printNota(string $id)
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
            $lebar_kolom_1 = 9;
            $lebar_kolom_2 = 15;
            $lebar_kolom_3 = 19;

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
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ", STR_PAD_RIGHT);

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

        $sales = Sales::find(auth()->user()->sales->id);
        $penjualan = Penjualan::find($id);
        $items = PenjualanDetail::where('penjualan_id', $id)->get();

        $connector = new WindowsPrintConnector("POS-80");
        $printer = new Printer($connector);

        //print nota order dengan menggunakan printer thermal
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("STRUK PEMBELIAN\n");
        $printer->setEmphasis(true);
        $printer->text($sales->sales_name."\n");
        $printer->setEmphasis(false);
        $printer->text($sales->address."\n");
        $printer->text("WA. ".$sales->sales_phone."\n");
        $printer->text("===============================================\n");
        $printer->setEmphasis(true);
        $printer->text($penjualan->no_invoice . "\n");
        $printer->setEmphasis(false);
        $printer->text("===============================================\n");
        $printer->text("\n");
        $printer->text(buatBaris2Kolom(date_format($penjualan->created_at ,'d F Y') , date_format($penjualan->created_at ,'H:i')));
        $printer->text(buatBaris2Kolom('Pelanggan', $penjualan->customer->nama));
        $printer->text(buatBaris2Kolom('Kasir', $sales->sales_name));
        $printer->text("-----------------------------------------------\n");

        foreach($items as $item){
            $qty = $item->jumlah . 'x';
            $harga = '@'. number_format($item->harga, 0, ',', '.');
            $printer->setEmphasis(true);
            $printer->text(buatBaris1Kolom($item->produk->nama_produk));
            $printer->setEmphasis(false);
            $printer->text(buatBaris3Kolom($qty, $harga , CustomHelper::addCurrencyFormat($item->harga * $item->jumlah)));
        }

        $printer->text("-----------------------------------------------\n");
        $printer->text(buatBaris2Kolom('Subtotal', CustomHelper::addCurrencyFormat($penjualan->total)));
        $printer->text(buatBaris2Kolom('Diskon', CustomHelper::addCurrencyFormat($penjualan->diskon)));
        $printer->text("-----------------------------------------------\n");
        $printer->text(buatBaris2Kolom('Cash/Transfer', CustomHelper::addCurrencyFormat($penjualan->diterima)));
        $printer->text(buatBaris2Kolom('Kembali', CustomHelper::addCurrencyFormat($penjualan->diterima - ($penjualan->total - $penjualan->diskon))));
        $printer->text("-----------------------------------------------\n");
        $printer->text("-- Terima kasih --\n");
        $printer->text("Selamat Datang Kembali\n");

        $printer->cut();
        $printer->close();
    }

    public function printNotaJson(string $id)
    {
        $sales = Sales::find(auth()->user()->sales->id);
        $penjualan = Penjualan::find($id);
        $items = PenjualanDetail::where('penjualan_id', $id)->get();

        return response()->json([
            'sales' => $sales,
            'penjualan' => $penjualan,
            'items' => $items
        ], 200);
    }

    public function notaPenjualan($id)
    {
        $penjualan = Penjualan::find($id);

        if (!$penjualan) {
            return response()->json(['message' => 'Tidak ditemukan penjualan !'], 404);
        }

        $items = PenjualanDetail::where('penjualan_id', $id)->get();

        //merge data penjualan dan item penjualan
        $data = [
            'penjualan' => $penjualan,
            'items' => $items
        ];

        return NotaResource::make($data);
    }

    public function detailTransaksi($id)
    {
        $penjualan = Penjualan::with(['customer', 'cabang'])->find($id);
        $items = PenjualanDetail::where('penjualan_id', $id)->get();
        $sales = Sales::find($penjualan->sales_id);

        return view('page.kasir.detail-transaksi', compact('penjualan', 'items', 'sales'));
    }
}
