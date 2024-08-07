<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Sales;
use App\Models\Barang;
use App\Models\Antrian;
use App\Models\Customer;
use App\Models\DataAntrian;
use Illuminate\Http\Request;
use App\Models\CategoryUsaha;
use App\Models\DailyActivity;
use App\Models\SocialAccount;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $antrians = Antrian::with('sales', 'customer', 'job')->where('status', '1')->get();

        return view('antrian.sales.index', compact('antrians'));
    }

    public function category()
    {
        $category = CategoryUsaha::all();

        return view('antrian.sales.category', compact('category'));
    }

    public function cekTelepon()
    {
        return view('antrian.sales.cek-telepon');
    }

    public function checkCustomer(Request $request)
    {
        $customer = Customer::where('customer_phone', $request->input('phone_number'))->first();

        if ($customer) {
            return redirect()->route('sales.create', ['customer' => $customer->customer_phone])->with('success', 'Pelanggan ditemukan !');
        } else {
            return redirect()->route('sales.create')->with('error', 'Pelanggan Baru ! Silahkan isi form dibawah ini.');
        }
    }

    public function create()
    {
        $sales = Sales::all();
        $jobs = Job::all();

        if (request()->get('customer')) {
            $customer = Customer::where('customer_phone', request()->get('customer'))->first();

        } else {
            $customer = null;
        }

        return view('antrian.sales.create', compact('jobs', 'sales', 'customer'));
    }

    public function store(Request $request)
    {
        // Jika data customer sudah terdaftar di database, hindari duplikasi data
        $customer = Customer::where('customer_phone', $request->input('noCustomer'))->first();

        // Validasi Data Customer
        $validated = $request->validate([
            'namaCustomer' => 'required',
            'noCustomer' => 'required',
            'alamatCustomer' => 'required',
            'infoCustomer' => 'required',
            'sales' => 'required',
            'job' => 'required',
            'note' => 'required',
            'omset' => 'required',
            'accDesign' => 'required | image | mimes:jpeg,png,jpg,gif,svg | max:2048',

        ]);

        // Simpan Foto Design
        $file = $request->file('accDesign');
        $nama_file = time().'_'.$file->getClientOriginalName();
        $tujuan_upload = 'storage/design-proof';
        $file->move($tujuan_upload, $nama_file);

        // Simpan Data Customer Baru
        if (! $customer) {
            $customer = Customer::create([
                'customer_name' => $validated['namaCustomer'],
                'customer_phone' => $validated['noCustomer'],
                'customer_address' => $validated['alamatCustomer'],
                'customer_info' => $validated['infoCustomer'],
            ]);
        }

        // Generate ticket_order dari tanggal + id antrian terakhir
        $lastAntrian = Antrian::latest()->first();
        $lastId = $lastAntrian ? $lastAntrian->id : 0;
        $ticket_order = date('Ymd').($lastId + 1);

        //simpan data antrian
        $antrian = Antrian::create([
            'customer_id' => $customer->id,
            'sales_id' => $validated['sales'],
            'job_id' => $validated['job'],
            'acc_design' => $nama_file,
            'note' => $validated['note'],
            'omset' => $validated['omset'],
            'ticket_order' => $ticket_order,
            'status' => '1',
        ]);

        return redirect()->route('sales.index')->with('success', 'Data Berhasil Ditambahkan !');

    }

    public function addCategory()
    {
        return view('antrian.sales.add-category');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required',
        ]);

        $category = CategoryUsaha::create([
            'category_name' => $validated['category_name'],
        ]);

        return redirect()->route('category.index')->with('success', 'Data Berhasil Ditambahkan !');
    }

    public function summaryReport(Request $request)
    {
        function hitungPersenOmsetTercapai($actualSales, $targetSales)
        {
            if ($targetSales === 0) {
                return 0;
            }

            $percentage = ($actualSales / $targetSales) * 100;
            return round($percentage, 2);
        }

        if(empty($request->sales_id)) {
            $salesId = Sales::where('id', 1)->first();
        } else {
            $salesId = Sales::where('id', $request->sales_id)->first();
        }

        $salesAll = Sales::all();

        //hitung total customer lead dan customer order from lead dalam 1 bulan
        $customerLead = Customer::where('sales_id', $salesId)->where('frekuensi_order', 0)->where('created_at', '>=', now()->subMonth())->count();
        $customerOrderFromLead = Customer::where('sales_id', $salesId)->where('frekuensi_order', 1)->where('created_at', '>=', now()->subMonth())->count();

        //hitung total pelanggan dalam 1 bulan
        $totalPelanggan = Customer::where('sales_id', $salesId)->where('created_at', '>=', now()->subMonth())->count();
        //hitung total pelanggan loyal dalam 1 bulan
        $pelangganLoyal = Customer::where('sales_id', $salesId)->where('frekuensi_order', '>=', 2)->where('created_at', '>=', now()->subMonth())->count();
        //hitung average pelanggan baru dalam 1 bulan
        $avgPelangganBaru = round($totalPelanggan / 30);
        //hitung total omset dalam 1 bulan
        $omsetBulanan = 0;
        $totalOmset = DataAntrian::where('sales_id', $salesId)->where('created_at', '>=', now()->subMonth())->get();
        foreach ($totalOmset as $omset) {
            $omsetBulanan += $omset->pembayaran->total_harga;
        }
        $actualSales = $omsetBulanan;
        $omsetBulanan = number_format($omsetBulanan, 0, ',', '.');
        //hitung omset sales dalam persen
        $targetSales = $salesId->target_omset;
        $dalamPersen = hitungPersenOmsetTercapai($actualSales, $targetSales);

        //ambil data sosmed sales
        $socialAccounts = SocialAccount::where('sales_id', $salesId->id)
            ->get();
        $socialAccountsByPlatform = $socialAccounts->groupBy('platform');
        $igs = $socialAccountsByPlatform['Instagram'] ?? collect();
        $fbs = $socialAccountsByPlatform['Facebook'] ?? collect();
        $tts = $socialAccountsByPlatform['Tiktok'] ?? collect();
        $yts = $socialAccountsByPlatform['Youtube'] ?? collect();
        $sps = $socialAccountsByPlatform['Shopee'] ?? collect();
        $tps = $socialAccountsByPlatform['Tokopedia'] ?? collect();

        //ambil data aktivitas harian sales
        $dailyActivities = DailyActivity::where('sales_id', $salesId->id)
            ->whereDate('created_at', '>=', now()->subMonth())
            ->get();
        //group daily activities by platform
        $dailyActivitiesByPlatform = $dailyActivities->groupBy('platform');
        $dailyActivitiesIgs = $dailyActivitiesByPlatform['Instagram'] ?? collect();
        $dailyActivitiesFbs = $dailyActivitiesByPlatform['Facebook'] ?? collect();
        $dailyActivitiesTts = $dailyActivitiesByPlatform['Tiktok'] ?? collect();
        $dailyActivitiesYts = $dailyActivitiesByPlatform['Youtube'] ?? collect();
        $dailyActivitiesSps = $dailyActivitiesByPlatform['Shopee'] ?? collect();
        $dailyActivitiesTps = $dailyActivitiesByPlatform['Tokopedia'] ?? collect();
        $dailyActivitiesWa = $dailyActivitiesByPlatform['Whatsapp'] ?? collect();

        //ambil data produk dengan omset tertinggi
        $productHighOmset = Barang::with('antrian.pembayaran', 'job')
            ->whereHas('antrian', function ($query) use ($salesId) {
                $query->where('sales_id', $salesId->id);
            })
            ->orderBy('price', 'desc')->limit(5)->get();

        return view('page.report.sales.summary-report', compact(
            'productHighOmset', 'customerLead', 'customerOrderFromLead', 'salesAll', 'igs', 'fbs', 'tts', 'yts', 'sps', 'tps', 'salesId', 'totalPelanggan', 'pelangganLoyal', 'avgPelangganBaru', 'omsetBulanan', 'dalamPersen', 'targetSales', 'actualSales', 'dailyActivitiesIgs', 'dailyActivitiesFbs', 'dailyActivitiesTts', 'dailyActivitiesYts', 'dailyActivitiesSps', 'dailyActivitiesTps', 'dailyActivitiesWa'
        ));
    }

    public function getSosmedByPlatform($platform)
    {
        $sosmed = SocialAccount::where('sales_id', auth()->user()->sales->id)->where('platform', $platform)->get();
        return response()->json($sosmed);
    }
}
