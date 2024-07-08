<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SumberPelanggan;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $customers = Customer::with('sales')->get();
        $salesAll = Sales::all()->pluck('sales_name', 'id');

        return view('page.customer.index', compact('customers', 'salesAll'));
    }

    public function indexJson()
    {
        $customers = Customer::with(['sales'])->get();
        return Datatables::of($customers)
        ->addIndexColumn()
        ->addColumn('sales', function ($customer) {
            return $customer->sales->sales_name ?? '-';
        })
        ->addColumn('telepon', function ($customer) {
            return $customer->telepon == null ? '-' : $customer->telepon;
        })
        ->addColumn('nama', function ($customer) {
            return $customer->nama == null ? '-' : $customer->nama;
        })
        ->addColumn('alamat', function ($customer) {
            return $customer->alamat ? Str::limit($customer->alamat, 30) : '-';
        })
        ->addColumn('instansi', function ($customer) {
            return $customer->instansi == null ? '-' : $customer->instansi;
        })
        ->addColumn('infoPelanggan', function ($customer) {
            return $customer->infoPelanggan == null ? '-' : $customer->infoPelanggan;
        })
        ->addColumn('provinsi', function ($customer) {
            return $customer->provinsi ?? '-';
        })
        ->addColumn('kota', function ($customer) {
            return $customer->kota ?? '-';
        })
        ->addColumn('action', function ($customer) {
            return '
            <div class="btn-group">
                <button class="btn btn-sm btn-primary" onclick="editForm(`'. route('customer.update', $customer->id) .'`)" ><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteForm(`'. route('customer.destroy', $customer->id) .'`)"><i class="fa fa-trash"></i></button> 
            </div>
            ';
        })
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

    public function edit(Request $request)
    {
        $customer = Customer::find($request->id);
        return response()->json($customer);
    }

    public function store(Request $request)
    {
        //Menyimpan no.telp dalam format seperti berikut 081234567890, tanpa spasi. strip, titik, dll
        $telp = preg_replace('/\D/', '', $request->telepon);

        if(substr($telp, 0, 1) == '0'){
            $telp = '62'.substr($telp, 1);
        }else{
            $telp = $telp;
        }

        $customer = new Customer;

        $customer->telepon = $telp;
        $customer->sales_id = $request->salesID;
        $customer->provinsi = $request->provinsi;
        $customer->kota = $request->kota;

        if($request->nama){
            $customer->nama = $request->nama;
        }

        if($request->alamat){
            $customer->alamat = $request->alamat;
        }

        if($request->instansi){
            $customer->instansi = $request->instansi;
        }

        if($request->modalInfoPelanggan){
            $customer->infoPelanggan = $request->infoPelanggan;
        }
        $customer->save();
        return response()->json(['success' => 'true', 'message' => 'Pelanggan berhasil ditambahkan !']);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
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

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return response()->json(['success' => 'true', 'message' => 'Data berhasil dihapus !']);
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
