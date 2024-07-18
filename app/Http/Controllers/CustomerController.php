<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Customer;
use App\Models\DataAntrian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SumberPelanggan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $salesAll = Sales::all()->pluck('sales_name', 'id');

        return view('page.customer.index', compact('salesAll'));
    }

    public function indexJson()
    {
        if(auth()->user()->role_id == 11){
            $customers = Customer::with(['sales', 'sumberPelanggan'])->where('sales_id', auth()->user()->sales->id)->get();
        }else{
            $customers = Customer::with(['sales', 'sumberPelanggan'])->get();
        }
        
        return Datatables::of($customers)
        ->addIndexColumn()
        ->addColumn('sales', function ($customer) {
            return $customer->sales->sales_name ?? '-';
        })
        ->addColumn('telepon', function ($customer) {
            $format = substr($customer->telepon, 0, 2);
            //jika format no 08xx maka diubah menjadi 628xx
            if($format == '08'){
                $telp = '62'.substr($customer->telepon, 1);
            }else{
                $telp = $customer->telepon;
            }
            return "<a class='text-success' href='https://wa.me/$telp'><i class='fab fa-whatsapp'></i> $customer->telepon</a>";
        })
        ->addColumn('nama', function ($customer) {
            $nama = Str::limit($customer->nama, 20);
            return "<a href='/customer/show/$customer->id'>$nama</a><br><small class='text-muted'>$customer->instansi</small>";
        })
        ->addColumn('infoPelanggan', function ($customer) {
            return $customer->sumberPelanggan->nama_sumber ?? '-';
        })
        ->addColumn('status', function ($customer) {
            $frekuensi = $customer->frekuensi_order;

            if($frekuensi == 0){
                $status = '<span class="badge badge-danger">Leads</span>';
            }elseif($frekuensi == 1){
                $status = '<span class="badge badge-warning">Pelanggan Baru</span>';
            }elseif($frekuensi > 1){
                $status = '<span class="badge badge-success">Repeat Order</span>';
            }

            return $status;
        })
        ->addColumn('action', function ($customer) {
            return '
            <div class="btn-group">
                <a href="'.route('customer.edit', $customer->id).'" class="btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>
                <button class="btn btn-sm btn-danger" onclick="deleteForm(`'. route('customer.destroy', $customer->id) .'`)"><i class="fa fa-trash"></i></button> 
            </div>
            ';
        })
        ->rawColumns(['action', 'nama', 'telepon', 'status'])
        ->make(true);
    }

    public function cariPelanggan(Request $request)
    {
        $data = Customer::where('nama', 'LIKE', "%".request('q')."%")->get();

        return response()->json($data);
    }

    public function pelangganById($id)
    {
        $customer = Customer::find($id);
        return response()->json($customer);
    }

    public function create()
    {
        $salesAll = Sales::all()->pluck('sales_name', 'id');
        $sumberAll = SumberPelanggan::all()->pluck('nama_sumber', 'id');

        return view('page.customer.create', compact('salesAll', 'sumberAll'));
    }

    private function getStatus($frekuensi)
    {
        if($frekuensi == 0){
            $status = 'Leads';
        }elseif($frekuensi == 1){
            $status = 'Pelanggan Baru';
        }elseif($frekuensi > 1){
            $status = 'Repeat Order';
        }

        return $status;
    }

    private function getFormatWa($telp)
    {
        $format = substr($telp, 0, 2);
        //jika format no 08xx maka diubah menjadi 628xx
        if($format == '08'){
            $telp = '62'.substr($telp, 1);
        }else{
            $telp = $telp;
        }

        return $telp;
    }

    private function getNamaProvinsiKota($kodeProvinsi, $kodeKota)
    {
        //Mengambil data provinsi dari API
        $provinsi = Http::get('https://sipedas.pertanian.go.id/api/wilayah/list_pro?thn=2024')->json();

        $namaProvinsi = $provinsi[$kodeProvinsi] ?? 'Provinsi tidak ditemukan';

        //Mengambil data kota dari API
        $kota = Http::get('https://sipedas.pertanian.go.id/api/wilayah/list_kab?thn=2024&lvl=11&pro=' . $kodeProvinsi)->json();
        
        $namaKota = $kota[$kodeKota] ?? 'Kota tidak ditemukan';

        return ['provinsi' => $namaProvinsi, 'kota' => $namaKota];
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        $status = $this->getStatus($customer->frekuensi_order);
        $telp = $this->getFormatWa($customer->telepon);

        $orders = DataAntrian::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $infoPelanggan = SumberPelanggan::all();
        $sumberPelanggan = SumberPelanggan::find($customer->infoPelanggan);

        $provdankota = $this->getNamaProvinsiKota($customer->provinsi, $customer->kota);

        return view('page.customer.show', compact('customer', 'status', 'telp', 'orders', 'infoPelanggan', 'provdankota', 'sumberPelanggan'));
    }

    public function store(Request $request)
    {
        $sales = auth()->user()->sales->id;
        //Menyimpan no.telp dalam format seperti berikut 081234567890, tanpa spasi. strip, titik, dll
        $telp = preg_replace('/\D/', '', $request->telepon);

        if(substr($telp, 0, 1) == '0'){
            $telp = '62'.substr($telp, 1);
        }else{
            $telp = $telp;
        }
        
        try {
            DB::transaction(function () use ($request, $telp) {
                $customer = Customer::create([
                    'nama' => $request->nama,
                    'telepon' => $telp,
                    'alamat' => $request->alamat,
                    'instansi' => $request->instansi,
                    'infoPelanggan' => $request->infoPelanggan,
                    'frekuensi_order' => 0,
                    'count_followUp' => 0,
                    'sales_id' => $sales,
                    'provinsi' => $request->provinsi,
                    'kota' => $request->kota,
                ]);
            });

            // Success message or logic after successful transaction
            return response()->json(['message' => 'Pelanggan berhasil ditambahkan!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Error message or logic for handling the exception
            return response()->json(['error' => 'Pelanggan gagal ditambahkan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        $salesAll = Sales::all()->pluck('sales_name', 'id');
        $sumberAll = SumberPelanggan::all();
        $wilayah = $this->getNamaProvinsiKota($customer->provinsi, $customer->kota);

        return view('page.customer.edit', compact('customer', 'salesAll', 'sumberAll', 'wilayah'));
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::find($id);
            $customer->nama = $request->nama;
            $customer->telepon = $request->telepon;
            $customer->alamat = $request->alamat;
            $customer->instansi = $request->instansi;
            $customer->infoPelanggan = $request->infoPelanggan;
            $customer->provinsi = $request->provinsi;
            $customer->kota = $request->kota;
            $customer->save();

            return response()->json(['success' => 'true', 'message' => 'Data berhasil diubah !'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => 'false', 'message' => 'Data gagal diubah !'], 500);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return response()->json(['success' => 'true', 'message' => 'Data berhasil dihapus !']);
    }

    public function export()
    {
        return Excel::download(new CustomerExport, 'customer.xlsx');
    }

    public function getAllCustomers(Request $request)
    {
        $searchTerm = $request->q;
        $sales = auth()->user()->sales->id;

        if($searchTerm == ''){
            $customers = Customer::where('sales_id', $sales)->orderBy('created_at', 'desc')->select('id', 'nama', 'telepon')->limit(10)->get();
        }else{
            $customers = Customer::where('sales_id', $sales)->orderBy('created_at', 'desc')->select('id', 'nama', 'telepon')->where('nama', 'LIKE', "%".$searchTerm."%")->orWhere('telepon', 'LIKE', "%".$searchTerm."%")->limit(10)->get();
        }

        return response()->json($customers);
    }

    public function getAllCustomersApi(Request $request)
    {
        $searchTerm = $request->q;
        $sales = $request->sales;

        if($searchTerm == ''){
            $customers = Customer::where('sales_id', $sales)->orderBy('created_at', 'desc')->select('id', 'nama', 'telepon')->limit(10)->get();
        }else{
            $customers = Customer::where('sales_id', $sales)->orderBy('created_at', 'desc')->select('id', 'nama', 'telepon')->where('nama', 'LIKE', "%".$searchTerm."%")->orWhere('telepon', 'LIKE', "%".$searchTerm."%")->limit(10)->get();
        }

        return response()->json($customers);
    }

    public function statusPelanggan($id)
    {
        $customer = Customer::find($id);

        $frekuensi = $customer->frekuensi_order;

        if($frekuensi == 0){
            $status = 'New Leads';
        }elseif($frekuensi > 0){
            $status = 'Pelanggan Baru';
        }elseif($frekuensi > 1){
            $status = 'Repeat Order';
        }else{
            $status = 'Sangat Sering';
        }

        return response()->json(['status' => $status]);
    }

    public function tambahProduk(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->nama = $request->namaPelanggan;
        $customer->telepon = $request->telepon;
        $customer->alamat = $request->alamat;
        $customer->instansi = $request->instansi;
        $customer->infoPelanggan = $request->infoPelanggan;
        $customer->wilayah = $request->wilayah;
        $customer->sales_id = $request->sales;
        $customer->save();

        return response()->json(['success' => 'true', 'message' => 'Data berhasil diubah !']);
    }

    public function getInfoPelanggan()
    {
        $sumberAll = SumberPelanggan::pluck('nama_sumber', 'id');

        return response()->json($sumberAll);
    }
}
