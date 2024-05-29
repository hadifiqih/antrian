<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Bahan;
use App\Models\Order;
use App\Models\Sales;
use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Antrian;
use App\Models\Machine;
use App\Models\Customer;
use App\Models\Employee;

use App\Models\Anservice;
use App\Models\DataKerja;
use App\Models\Ekspedisi;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use App\Models\DataAntrian;
use App\Models\DesignQueue;
use App\Models\Dokumproses;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\BiayaProduksi;
use App\Models\Documentation;
use App\Models\BuktiPembayaran;
use App\Models\SumberPelanggan;
use App\Notifications\AntrianWorkshop;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AntrianDiantrikan;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;

class AntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //--------------------------------------------------------------------------
    //Fungsi untuk menampilkan halaman tambah antrian workshop
    //--------------------------------------------------------------------------

    public function index()
    {
        $jobs = Job::all();
        $cabang = Cabang::all();
        $sales = Sales::all();
        return view('page.antrian-workshop.index', compact('jobs', 'cabang', 'sales'));
    }

    public function indexData(Request $request)
    {
        // Descriptive variable names
        $productId = $request->get('produk');
        $branchId = $request->get('cabang');
        $salesId = $request->get('sales');
        // Build the query with eager loading
        $antrians = DataAntrian::with('sales', 'customer', 'job', 'barang', 'dataKerja', 'cabang', 'buktiBayar')
            ->where('status', 1) // Ensure active entries
            ->orderByDesc('created_at');
        // Apply filters if any parameters are provided
        if ($request->has('kategori') || $request->has('cabang') || $request->has('sales')) {
            if ($productId !== null) {
                $antrians->whereHas('barang', function ($query) use ($productId) {
                    $query->where('job_id', $productId);
                });
            }
            if ($branchId !== null) {
                $antrians->where('cabang_id', $branchId);
            }
            if ($salesId !== null) {
                $antrians->where('sales_id', $salesId);
            }
        }
        // Execute the query and return results
        $antrians = $antrians->get();

        return DataTables::of($antrians)
            ->addIndexColumn()
            ->addColumn('ticket_order', function ($antrian) {
                return '<a href="' . route('antrian.show', $antrian->ticket_order) . '">' . $antrian->ticket_order . '</a>';
            })
            ->addColumn('sales', function ($antrian) {
                return $antrian->sales->sales_name;
            })
            ->addColumn('customer', function ($antrian) {
                return $antrian->customer->nama;
            })
            ->addColumn('action', function ($antrian) {
                $btn = '<div class="btn-group">';
                if(auth()->user()->isAdminWorkshop()) {
                    if($antrian->dataKerja->tgl_selesai == null) {
                        $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm disabled"><i class="fas fa-download"></i> e-SPK</a>';
                    } else {
                        $btn .= '<a href="'.route('antrian.form-espk', $antrian->ticket_order).'"  class="btn btn-success btn-sm"><i class="fas fa-download"></i> e-SPK</a>';
                    }
                    $btn .= '<a href="' . route('antrian.edit', $antrian->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Penugasan</a>';
                    $btn .= '<a href="'.route('antrian.show', $antrian->ticket_order).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>';
                    $btn .= '<a href="javascript:void(0)" onclick="deleteAntrian('.$antrian->ticket_order.')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</a>';
                } elseif(auth()->user()->isProduksi()) {
                    $btn .= '<a href="' . route('documentation.uploadProduksi', $antrian->ticket_order) . '" class="btn btn-warning btn-sm"><i class="fas fa-camera"></i> Unggah Dokumentasi</a>';
                } elseif(auth()->user()->isSales()) {
                    $btn .= '<a href="'. route('order.notaOrder', $antrian->ticket_order) .'" class="btn btn-info btn-sm"><i class="fas fa-print"></i>Print Struk</a>';
                } else {
                    $btn .= '<a href="'.route('antrian.show', $antrian->ticket_order).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['ticket_order', 'action'])
            ->make(true);
    }

    public function indexSelesai(Request $request)
    {
        // Descriptive variable names
        $productId = $request->get('produk');
        $branchId = $request->get('cabang');
        $salesId = $request->get('sales');

        // Build the query with eager loading
        $antrians = DataAntrian::with('sales', 'customer', 'job', 'barang', 'dataKerja', 'cabang', 'buktiBayar')
            ->where('status', 2) // Ensure active entries
            ->orderByDesc('created_at');

        // Apply filters if any parameters are provided
        if ($request->has('kategori') || $request->has('cabang') || $request->has('sales')) {
            if ($productId !== null) {
                $antrians->whereHas('barang', function ($query) use ($productId) {
                    $query->where('job_id', $productId);
                });
            }

            if ($branchId !== null) {
                $antrians->where('cabang_id', $branchId);
            }

            if ($salesId !== null) {
                $antrians->where('sales_id', $salesId);
            }
        }

        // Execute the query and return results
        $antrians = $antrians->get();

        return DataTables::of($antrians)
            ->addIndexColumn()
            ->addColumn('tanggal_order', function ($antrian) {
                return $antrian->created_at->format('d-m-Y');
            })
            ->addColumn('ticket_order', function ($antrian) {
                return '<a href="' . route('antrian.show', $antrian->ticket_order) . '">' . $antrian->ticket_order . '</a>';
            })
            ->addColumn('sales', function ($antrian) {
                return $antrian->sales->sales_name;
            })
            ->addColumn('customer', function ($antrian) {
                return $antrian->customer->nama;
            })
            ->addColumn('action', function ($antrian) {
                $btn = '<div class="btn-group">';
                if(auth()->user()->isSales()) {
                    $btn .= '<a href="'. route('order.notaOrder', $antrian->ticket_order) .'" class="btn btn-dark btn-sm"><i class="fas fa-print"></i> Print Struk</a>';
                }
                $btn .= '<a href="'.route('antrian.show', $antrian->ticket_order).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action','ticket_order'])
            ->make(true);

    }

    public function buatAntrianWorkshop()
    {
        $ekspedisi = Ekspedisi::all();
        $desain = DesignQueue::where('sales_id', auth()->user()->sales->id)->where('status', 2)->where('data_antrian_id', 0)->get();
        $infoPelanggan = SumberPelanggan::all();

        return view('page.antrian-workshop.create', compact('ekspedisi', 'desain', 'infoPelanggan'));
    }

    public function printeSpk($id)
    {
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        $dataKerja = DataKerja::where('ticket_order', $id)->first();
        $order = Order::where('ticket_order', $id)->first();
        $customer = Customer::where('id', $antrian->customer_id)->first();
        $sales = Sales::where('id', $antrian->sales_id)->first();
        $job = Job::where('id', $antrian->job_id)->first();
        $barang = Barang::where('ticket_order', $id)->get();
        $cabang = Cabang::where('id', $antrian->cabang_id)->first();

        return view('page.antrian-workshop.modal.modal-form-spk', compact('antrian', 'dataKerja', 'order', 'customer', 'sales', 'job', 'barang', 'cabang'));
    }

    //--------------------------------------------------------------------------
    //Filter antrian berdasarkan kategori pekerjaan
    //--------------------------------------------------------------------------

    public function filterProcess(Request $request)
    {
        $jobType = $request->input('kategori');

        $filtered = $jobType;

        return view('page.antrian-workshop.index', compact('filtered'));
    }

    //--------------------------------------------------------------------------
    //Fungsi untuk menampilkan halaman tambah antrian service
    //--------------------------------------------------------------------------

    public function serviceCreate()
    {
        return view('page.antrian-service.create');
    }

    //--------------------------------------------------------------------------
    //Estimator
    //--------------------------------------------------------------------------

    public function estimatorIndex(Request $request)
    {
        if($request->input('kategori')) {
            $jobType = $request->input('kategori');
            $filtered = $jobType;

            $fileBaruMasuk = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
            ->whereHas('job', function ($query) use ($jobType) {
                $query->where('job_type', $jobType);
            })
            ->where('status', '1')
            ->orderByDesc('created_at')
            ->get();

            $progressProduksi = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
            ->whereHas('job', function ($query) use ($jobType) {
                $query->where('job_type', $jobType);
            })
            ->where('status', '1')
            ->orderByDesc('created_at')
            ->get();

            $selesaiProduksi = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
            ->whereHas('job', function ($query) use ($jobType) {
                $query->where('job_type', $jobType);
            })
            ->where('status', '2')
            ->orderByDesc('created_at')
            ->take(75)
            ->get();

            return view('page.antrian-workshop.estimator-index', compact('fileBaruMasuk', 'progressProduksi', 'selesaiProduksi', 'filtered'));

        } else {

            $filtered = null;

            $fileBaruMasuk = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
            ->where('status', '1')
            ->where('is_aman', '0')
            ->orderByDesc('created_at')
            ->get();

            $progressProduksi = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
            ->where('status', '1')
            ->orderByDesc('created_at')
            ->get();

            $selesaiProduksi = Antrian::with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
            ->where('status', '2')
            ->orderByDesc('created_at')
            ->take(75)
            ->get();

            return view('page.antrian-workshop.estimator-index', compact('fileBaruMasuk', 'progressProduksi', 'selesaiProduksi'));
        }
    }

    //--------------------------------------------------------------------------
    //Admin Sales
    //--------------------------------------------------------------------------

    public function omsetGlobal()
    {
        $listSales = Sales::all();
        //mengambil tanggal awal dan tanggal akhir dari bulan ini
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        //menyimpan tanggal menjadi array
        $dateRange = [];
        $date = $startDate;
        while($date->lte($endDate)) {
            $dateRange[] = $date->format('Y-m-d');
            $date->addDay();
        }

        //mengambil total omset per hari dari seluru sales
        $omsetPerHari = [];
        foreach($dateRange as $date) {
            $omset = Antrian::whereDate('created_at', $date)->sum('omset');
            $omsetPerHari[] = $omset;
        }

        return view('page.admin-sales.omset-global', compact('listSales', 'omsetPerHari', 'dateRange'));
    }

    public function downloadPrintFile($id)
    {
        $antrian = Barang::where('id', $id)->first();
        if($antrian->file_cetak == null) {
            return redirect()->back()->with('error', 'File cetak tidak ditemukan !');
        }
        $file = $antrian->barang->file_cetak;
        $path = storage_path('app/public/file-cetak/' . $file);
        return response()->download($path);
    }

    public function downloadPrintFileCreate($id)
    {
        $order = Order::where('id', $id)->first();
        $file = $order->file_cetak;
        $path = storage_path('app/public/file-cetak/' . $file);
        return response()->download($path);
    }

    public function downloadProduksiFile($id)
    {
        $antrian = Antrian::where('id', $id)->first();
        $file = $antrian->design->filename;
        $path = storage_path('app/public/file-jadi/' . $file);
        return response()->download($path);
    }

    public function downloadFilePendukung($id)
    {
        $antrian = DataAntrian::where('id', $id)->first();
        $file = $antrian->filePendukung->nama_file;
        $path = storage_path('app/public/file-pendukung/' . $file);
        return response()->download($path);
    }

    public function simpanAntrian(Request $request)
    {
        // Dapatkan ID terakhir
        $lastId = DataAntrian::latest()->value('id');
        $lastId = $lastId ? $lastId + 1 : 1;
        $ticketOrder = Carbon::now()->format('Ymd') . $lastId;
        $customer = Customer::where('id', $request->input('customer_id'))->first();

        // Simpan antrian
        $antrian = DataAntrian::create([
            'ticket_order' => $ticketOrder,
            'sales_id' => auth()->user()->sales->id,
            'customer_id' => $request->input('customer_id'),
            'termasuk_pajak' => $request->input('termasukPajak'),
            'ppn' => $request->input('ppn') != '' ? CustomHelper::removeCurrencyFormat($request->input('ppn')) : 0,
            'pph' => $request->input('pph') != '' ? CustomHelper::removeCurrencyFormat($request->input('pph')) : 0,
            'status' => 1,
        ]);

        // Simpan data barang
        Barang::where('customer_id', $request->input('customer_id'))
            ->whereNull('ticket_order')
            ->update(['ticket_order' => $ticketOrder]);

        // Simpan pembayaran
        $payment = Pembayaran::create([
            'ticket_order' => $ticketOrder,
            'metode_pembayaran' => $request->input('metodePembayaran'),
            'biaya_packing' => $request->input('biayaPacking') ? CustomHelper::removeCurrencyFormat($request->input('biayaPacking')) : 0,
            'biaya_pasang' => $request->input('biayaPasang') ? CustomHelper::removeCurrencyFormat($request->input('biayaPasang')) : 0,
            'diskon' => $request->input('diskon') ? CustomHelper::removeCurrencyFormat($request->input('diskon')) : 0,
            'total_harga' => CustomHelper::removeCurrencyFormat($request->input('totalAllInput')),
            'dibayarkan' => CustomHelper::removeCurrencyFormat($request->input('jumlahPembayaran')),
            'status_pembayaran' => $request->input('statusPembayaran'),
        ]);


        // Simpan bukti pembayaran
        if ($request->hasFile('paymentImage')) {
            $buktiPembayaran = $request->file('paymentImage');
            $namaBaru = time() . '_' . $buktiPembayaran->getClientOriginalName();
            $path = 'bukti-pembayaran/' . $namaBaru;
            Storage::disk('public')->put($path, file_get_contents($buktiPembayaran));

            BuktiPembayaran::create([
                'ticket_order' => $ticketOrder,
                'gambar' => $namaBaru,
            ]);
        }

        // Periksa apakah pembayaran penuh atau parsial
        if ($payment->total_harga == $payment->dibayarkan) {
            $payment->update([
                'nominal_pelunasan' => $payment->dibayarkan,
                'file_pelunasan' => $namaBaru,
                'tanggal_pelunasan' => Carbon::now(),
                'status_pembayaran' => 2,
            ]);
        }

        // Simpan data kerja
        DataKerja::create([
            'ticket_order' => $ticketOrder,
        ]);

        // Perbarui frekuensi order pelanggan
        Customer::where('id', $request->input('customer_id'))
                ->when($antrian->customer_id != $customer->id || $antrian->created_at->format('Y-m-d') != Carbon::now()->format('Y-m-d'), function ($query) {
                    $query->increment('frekuensi_order');
                });

        return redirect()->route('antrian.index')->with('success', 'Data antrian berhasil ditambahkan!');
    }

    public function penugasanOtomatis(Request $request)
    {
        $antrian = DataAntrian::find($request->input('id'));
        $dataKerja = DataKerja::where('ticket_order', $antrian->ticket_order)->first();

        $pekerjaTerpilih = [];
        $pekerja = Employee::where('can_stempel', 1)->orWhere('can_adv', 1)->get();

        return response()->json($pekerja);
    }

    public function store(Request $request)
    {
        // $user = User::where('role', 'admin')->first();
        // $user->notify(new AntrianWorkshop($antrian, $order, $payment));
        // // Menampilkan push notifikasi saat selesai
        // $beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
        //     "instanceId" => "0958376f-0b36-4f59-adae-c1e55ff3b848",
        //     "secretKey" => "9F1455F4576C09A1DE06CBD4E9B3804F9184EF91978F3A9A92D7AD4B71656109",
        // ));

        // $publishResponse = $beamsClient->publishToInterests(
        //     array('admin'),
        //     array("web" => array("notification" => array(
        //       "title" => "📣 Cek sekarang, ada antrian baru !",
        //       "body" => "Cek antrian workshop sekarang, jangan sampai lupa diantrikan ya !",
        //     )),
        // ));

        // return redirect()->route('antrian.index')->with('success', 'Data antrian berhasil ditambahkan!');
    }

    public function editLama($id)
    {
        $antrian = DataAntrian::where('id', $id)->first();

        $operators = Employee::where('can_stempel', 1)->orWhere('can_adv', 1)->get();
        $qualitys = Employee::where('can_qc', 1)->get();

        //Melakukan explode pada operator_id, finisher_id, dan qc_id
        $operatorId = explode(',', $antrian->dataKerja->operator_id);
        $finishingId = explode(',', $antrian->dataKerja->finishing_id);
        $qualityId = explode(',', $antrian->dataKerja->qc_id);
        $cabangId = explode(',', $antrian->cabang_id);

        $machines = Machine::get();

        $totalHargaBarang = 0;
        $barangs = Barang::where('ticket_order', $antrian->ticket_order)->get();
        foreach($barangs as $barang) {
            $totalHargaBarang += $barang->price * $barang->qty;
        }
        $totalHargaBarang = number_format($totalHargaBarang, 0, ',', '.');
        $totalBarang = $barangs->sum('qty') . ' pcs';

        $tempatCabang = Cabang::pluck('nama_cabang', 'id');

        if($antrian->end_job == null) {
            $isEdited = 0;
        } else {
            $isEdited = 1;
        }

        return view('page.antrian-workshop.edit', compact('barangs', 'antrian', 'operatorId', 'finishingId', 'qualityId', 'cabangId', 'operators', 'qualitys', 'machines', 'tempatCabang', 'isEdited', 'totalHargaBarang', 'totalBarang'));
    }

    public function edit($id)
    {
        $antrian = DataAntrian::where('id', $id)->first();

        $operators = Employee::where(function ($query) {
                        $query->where('can_stempel', 1)
                            ->orWhere('can_adv', 1);
                        })->where('is_active', 1)->get();

        $qualitys = Employee::where('can_qc', 1)->where('is_active', 1)->get();

        $machines = Machine::get();

        $totalHargaBarang = 0;
        $barangs = Barang::where('ticket_order', $antrian->ticket_order)->get();
        foreach($barangs as $barang) {
            $totalHargaBarang += $barang->price * $barang->qty;
        }
        $totalHargaBarang = number_format($totalHargaBarang, 0, ',', '.');
        $totalBarang = $barangs->sum('qty') . ' pcs';

        $tempatCabang = Cabang::pluck('nama_cabang', 'id');

        if($antrian->end_job == null) {
            $isEdited = 0;
        } else {
            $isEdited = 1;
        }

        return view('page.antrian-workshop.edit', compact('barangs', 'antrian', 'operators', 'qualitys', 'machines', 'tempatCabang', 'isEdited', 'totalHargaBarang', 'totalBarang'));
    }

    public function updateLama(Request $request, $id)
    {

        $antrian = DataAntrian::find($id);
        $dataKerja = DataKerja::where('ticket_order', $antrian->ticket_order)->first();

        //Jika input operator adalah array, lakukan implode lalu simpan ke database
        $operator = implode(',', $request->input('operator_id'));
        $dataKerja->operator_id = $operator;

        //Jika input finisher adalah array, lakukan implode lalu simpan ke database
        $finisher = implode(',', $request->input('finishing_id'));
        $dataKerja->finishing_id = $finisher;

        //Jika input quality adalah array, lakukan implode lalu simpan ke database
        $quality = implode(',', $request->input('qc_id'));
        $dataKerja->qc_id = $quality;

        //start_job diisi dengan waktu sekarang
        $dataKerja->tgl_mulai = $request->input('start_job');
        $dataKerja->tgl_selesai = $request->input('end_job');

        //Jika input mesin adalah array, lakukan implode lalu simpan ke database
        if($request->input('jenisMesin')) {
            $mesin = implode(',', $request->input('jenisMesin'));
            $dataKerja->machine_id = $mesin;
        }
        $dataKerja->save();

        //Jika input tempat adalah array, lakukan implode lalu simpan ke database
        $tempat = implode(',', $request->input('cabang_id'));
        $antrian->cabang_id = $tempat;

        $antrian->admin_note = $request->input('admin_note');
        $antrian->save();

        // Menampilkan push notifikasi saat selesai
        $beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
            "instanceId" => "0958376f-0b36-4f59-adae-c1e55ff3b848",
            "secretKey" => "9F1455F4576C09A1DE06CBD4E9B3804F9184EF91978F3A9A92D7AD4B71656109",
        ));

        $users = [];

        foreach($request->input('operator_id') as $operator) {
            $user = 'user-' . $operator;
            $users[] = $user;
        }

        foreach($request->input('finishing_id') as $finisher) {
            $user = 'user-' . $finisher;
            $users[] = $user;
        }

        foreach($request->input('qc_id') as $quality) {
            $user = 'user-' . $quality;
            $users[] = $user;
        }
        // if($request->isEdited == 0){
        //     foreach($users as $user){
        //         $publishResponse = $beamsClient->publishToUsers(
        //             array($user),
        //             array("web" => array("notification" => array(
        //             "title" => "📣 Cek sekarang, ada pekerjaan baru !",
        //             "body" => "Cek pekerjaan baru sekarang, semangattt !",
        //             )),
        //         ));

        //         $user = str_replace('user-', '', $user);
        //         $user = User::find($user);
        //         if($user != 'rekananSBY' || $user != 'rekananKDR' || $user != 'rekananMLG'){
        //             $user->notify(new AntrianDiantrikan($antrian));
        //         }
        //     }
        // }else{
        //     foreach($users as $user){
        //         if($user != 'user-rekananSBY' || $user != 'user-rekananKDR' || $user != 'user-rekananMLG'){
        //             $publishResponse = $beamsClient->publishToUsers(
        //                 array($user),
        //                 array("web" => array("notification" => array(
        //                 "title" => "📣 Hai, ada update antrian!",
        //                 "body" => "Ada perubahan pada antrian " . $antrian->ticket_order . " (" . $antrian->order->title ."), cek sekarang !",
        //                 )),
        //             ));
        //         }

        //         $user = str_replace('user-', '', $user);
        //         $user = User::find($user);
        //         if($user != 'rekananSBY' || $user != 'rekananKDR' || $user != 'rekananMLG'){
        //             $user->notify(new AntrianDiantrikan($antrian));
        //         }
        //     }
        // }

        return redirect()->route('antrian.index')->with('success-update', 'Data antrian berhasil diupdate!');
    }

    public function update(Request $request, $id)
    {
        //Jika input operator adalah array, lakukan implode lalu simpan ke database
        $operator = implode(',', $request->input('operator_id'));

        //Jika input finisher adalah array, lakukan implode lalu simpan ke database
        $finisher = implode(',', $request->input('finishing_id'));

        //Jika input quality adalah array, lakukan implode lalu simpan ke database
        $quality = implode(',', $request->input('qc_id'));

        //Jika input tempat adalah array, lakukan implode lalu simpan ke database
        $tempat = implode(',', $request->input('cabang_id'));

        //Jika input mesin adalah array, lakukan implode lalu simpan ke database
        if($request->input('jenisMesin')) {
            $mesin = implode(',', $request->input('jenisMesin'));
        }

        $cekDataKerja = DataKerja::where('barang_id', $id)->where('ticket_order', $request->input('ticketOrder'))->first();
        if($cekDataKerja) {
            $cekDataKerja->barang_id = $id;
            $cekDataKerja->operator_id = $operator;
            $cekDataKerja->finishing_id = $finisher;
            $cekDataKerja->qc_id = $quality;
            $cekDataKerja->tgl_mulai = $request->input('start_job');
            $cekDataKerja->tgl_selesai = $request->input('end_job');
            $cekDataKerja->machine_id = $mesin ?? null;
            $cekDataKerja->cabang_id = $tempat;
            $cekDataKerja->admin_note = $request->input('admin_note');
            $cekDataKerja->save();
        } else {
            $dataKerja = new DataKerja();
            $dataKerja->ticket_order = $request->input('ticketOrder');
            $dataKerja->barang_id = $id;
            $dataKerja->operator_id = $operator;
            $dataKerja->finishing_id = $finisher;
            $dataKerja->qc_id = $quality;
            $dataKerja->tgl_mulai = $request->input('start_job');
            $dataKerja->tgl_selesai = $request->input('end_job');
            $dataKerja->machine_id = $mesin ?? null;
            $dataKerja->cabang_id = $tempat;
            $dataKerja->admin_note = $request->input('admin_note');
            $dataKerja->save();
        }

        // Menampilkan push notifikasi saat selesai
        $beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
            "instanceId" => "0958376f-0b36-4f59-adae-c1e55ff3b848",
            "secretKey" => "9F1455F4576C09A1DE06CBD4E9B3804F9184EF91978F3A9A92D7AD4B71656109",
        ));

        $users = [];

        foreach($request->input('operator_id') as $operator) {
            $user = 'user-' . $operator;
            $users[] = $user;
        }

        foreach($request->input('finishing_id') as $finisher) {
            $user = 'user-' . $finisher;
            $users[] = $user;
        }

        foreach($request->input('qc_id') as $quality) {
            $user = 'user-' . $quality;
            $users[] = $user;
        }
        // if($request->isEdited == 0){
        //     foreach($users as $user){
        //         $publishResponse = $beamsClient->publishToUsers(
        //             array($user),
        //             array("web" => array("notification" => array(
        //             "title" => "📣 Cek sekarang, ada pekerjaan baru !",
        //             "body" => "Cek pekerjaan baru sekarang, semangattt !",
        //             )),
        //         ));

        //         $user = str_replace('user-', '', $user);
        //         $user = User::find($user);
        //         if($user != 'rekananSBY' || $user != 'rekananKDR' || $user != 'rekananMLG'){
        //             $user->notify(new AntrianDiantrikan($antrian));
        //         }
        //     }
        // }else{
        //     foreach($users as $user){
        //         if($user != 'user-rekananSBY' || $user != 'user-rekananKDR' || $user != 'user-rekananMLG'){
        //             $publishResponse = $beamsClient->publishToUsers(
        //                 array($user),
        //                 array("web" => array("notification" => array(
        //                 "title" => "📣 Hai, ada update antrian!",
        //                 "body" => "Ada perubahan pada antrian " . $antrian->ticket_order . " (" . $antrian->order->title ."), cek sekarang !",
        //                 )),
        //             ));
        //         }

        //         $user = str_replace('user-', '', $user);
        //         $user = User::find($user);
        //         if($user != 'rekananSBY' || $user != 'rekananKDR' || $user != 'rekananMLG'){
        //             $user->notify(new AntrianDiantrikan($antrian));
        //         }
        //     }
        // }

        return redirect()->route('antrian.index')->with('success', 'Data antrian berhasil diupdate!');
    }

    public function show($id)
    {
        $tiket = $id;

        $antrian = DataAntrian::where('ticket_order', $id)->first();

        $items = Barang::where('ticket_order', $id)->get();

        $pembayaran = Pembayaran::where('ticket_order', $id)->first();

        $pengiriman = Pengiriman::where('ticket_order', $id)->first();

        $ekspedisi = Ekspedisi::all();

        $omset = $pembayaran->total_harga;

        $satuPersen = 1;
        $duaPersen = 2;
        $duaSetengahPersen = 2.5;
        $tigaPersen = 3;
        $limaPersen = 5;

        $total = 0;

        foreach($items as $item) {
            $subtotal = $item->price * $item->qty;
            $total += $subtotal;
        }

        $bahan = Bahan::where('ticket_order', $id)->get();

        $totalBahan = 0;

        foreach($bahan as $b) {
            $totalBahan += $b->harga;
        }

        $biayaSales = ($omset * $tigaPersen) / 100;
        $biayaDesain = ($omset * $duaPersen) / 100;
        $biayaPenanggungJawab = ($omset * $tigaPersen) / 100;
        $biayaPekerjaan = ($omset * $limaPersen) / 100;
        $biayaBPJS = ($omset * $duaSetengahPersen) / 100;
        $biayaTransportasi = ($omset * $satuPersen) / 100;
        $biayaOverhead = ($omset * $duaSetengahPersen) / 100;
        $biayaAlatListrik = ($omset * $duaPersen) / 100;

        $totalBiaya = $biayaSales + $biayaDesain + $biayaPenanggungJawab + $biayaPekerjaan + $biayaBPJS + $biayaTransportasi + $biayaOverhead + $biayaAlatListrik;

        $profit = $omset - $totalBiaya;

        $sisaPembayaran = $total - $pembayaran->dibayarkan;

        return view('page.antrian-workshop.show', compact('tiket', 'antrian', 'total', 'items', 'pembayaran', 'bahan', 'totalBahan', 'biayaSales', 'biayaDesain', 'biayaPenanggungJawab', 'biayaPekerjaan', 'biayaBPJS', 'biayaTransportasi', 'biayaOverhead', 'biayaAlatListrik', 'totalBiaya', 'profit', 'pengiriman', 'ekspedisi', 'sisaPembayaran'));
    }

    public function updateDeadline(Request $request)
    {
        $antrian = Antrian::find($request->id);
        if (now() > $antrian->end_job) {
            $status = 2;
        }
        $antrian->deadline_status = $status;
        $antrian->save();

        return response()->json(['message' => 'Success'], 200);
    }

    public function destroy($id)
    {
        // Melakukan pengecekan otorisasi sebelum menghapus antrian
        $this->authorize('delete', DataAntrian::class);

        $antrian = DataAntrian::where('ticket_order', $id)->first();

        $order = Order::where('ticket', $id)->first();
        $order->toWorkshop = 0;
        $order->save();

        if ($antrian) {
            $antrian->delete();
            return redirect()->route('antrian.index')->with('success-delete', 'Data antrian berhasil dihapus!');
        } else {
            return redirect()->route('antrian.index')->with('error-delete', 'Data antrian gagal dihapus!');
        }
    }
    //--------------------------------------------------------------------------

    public function tambahDesain()
    {
        $list_antrian = Antrian::get();
        return view('antriandesain.create', compact('list_antrian'));
    }

    //fungsi untuk menggunggah & menyimpan file gambar dokumentasi
    public function showDokumentasi($id)
    {
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        return view('page.antrian-workshop.dokumentasi', compact('antrian'));
    }

    public function storeDokumentasi(Request $request)
    {
        $files = $request->file('files');
        $id = $request->input('idAntrian');

        foreach($files as $file) {
            $filename = time()."_".$file->getClientOriginalName();
            $path = 'dokumentasi/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file));

            $dokumentasi = new Documentation();
            $dokumentasi->antrian_id = $id;
            $dokumentasi->filename = $filename;
            $dokumentasi->type_file = $file->getClientOriginalExtension();
            $dokumentasi->path_file = $path;
            $dokumentasi->job_id = $request->input('jobType');
            $dokumentasi->save();
        }

        return response()->json(['success' => 'You have successfully upload file.']);
    }

    public function getMachine(Request $request)
    {
        //Menampilkan data mesin pada tabel Machines
        $search = $request->input('search');

        if($search == '') {
            $machines = Machine::get();
        } else {
            $machines = Machine::where('machine_name', 'like', '%'.$search.'%')->get();
        }

        $response = array();
        foreach($machines as $machine) {
            $response[] = array(
                "id" => $machine->id,
                "text" => $machine->machine_name
            );
        }

        return response()->json($response);
    }

    public function showProgress($id)
    {
        $antrian = Antrian::where('id', $id)->with('job', 'sales', 'order')
        ->first();

        return view('page.antrian-workshop.progress', compact('antrian'));
    }

    public function storeProgressProduksi(Request $request)
    {
        $antrian = Antrian::where('id', $request->input('idAntrian'))->first();

        if($request->file('fileGambar')) {
            $gambar = $request->file('fileGambar');
            $namaGambar = time()."_".$gambar->getClientOriginalName();
            $pathGambar = 'dokum-proses/'.$namaGambar;
            Storage::disk('public')->put($pathGambar, file_get_contents($gambar));
        } else {
            $namaGambar = null;
        }

        if($request->file('fileVideo')) {
            $video = $request->file('fileVideo');
            $namaVideo = time()."_".$video->getClientOriginalName();
            $pathVideo = 'dokum-proses/'.$namaVideo;
            Storage::disk('public')->put($pathVideo, file_get_contents($video));
        } else {
            $namaVideo = null;
        }

        $dokumProses = new Dokumproses();
        $dokumProses->note = $request->input('note');
        $dokumProses->file_gambar = $namaGambar;
        $dokumProses->file_video = $namaVideo;
        $dokumProses->antrian_id = $request->input('idAntrian');
        $dokumProses->save();

        return redirect()->route('antrian.index');
    }

    public function markAman($id)
    {
        $design = Antrian::find($id);
        $design->is_aman = 1;
        $design->save();

        return redirect()->back()->with('success', 'File berhasil di tandai aman');
    }

    public function markSelesai($id)
    {
        //cek apakah documentasi sudah diupload
        $barang = Barang::where('ticket_order', $id)->get();

        foreach($barang as $b) {
            if($b->documentation_id == null) {
                return redirect()->back()->with('error', 'Ada barang yang belum di dokumentasi !');
            }
        }

        $datakerja = DataKerja::where('ticket_order', $id)->first();
        if($datakerja->operator_id == null || $datakerja->finishing_id == null || $datakerja->qc_id == null) {
            return redirect()->back()->with('error', 'Data penugasan belum lengkap !');
        }

        //cek apakah waktu sekarang sudah melebihi waktu deadline
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        $antrian->finish_date = Carbon::now();
        $antrian->status = 2;
        $antrian->save();

        // Menampilkan push notifikasi saat selesai
        $beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
           "instanceId" => "0958376f-0b36-4f59-adae-c1e55ff3b848",
           "secretKey" => "9F1455F4576C09A1DE06CBD4E9B3804F9184EF91978F3A9A92D7AD4B71656109",
        ));

        $publishResponse = $beamsClient->publishToInterests(
            array("sales"),
            array("web" => array("notification" => array(
                "title" => "Antree",
                "body" => "Yuhuu! Pekerjaan dengan tiket " . $antrian->ticket_order . " (" . $antrian->order->title ."), dari sales ". $antrian->sales->sales_name ." udah selesai !",
                "deep_link" => "https://app.kassabsyariah.com/",
            )),
        )
        );

        return redirect()->route('antrian.index')->with('success', 'Berhasil ditandai selesai !');
    }

    public function biayaProduksiSelesai(Request $request, $id)
    {
        $antrian = DataAntrian::where('ticket_order', $id)->first();
        $antrian->done_production_at = Carbon::now();
        $antrian->estimator_id = auth()->user()->employee->id;

        $omset = $request->input('omsetTotal');
        $omset = str_replace(['Rp', '.'], '', $omset);
        $omset = (int)$omset;

        $bproduksi = new BiayaProduksi();
        $bproduksi->ticket_order = $id;
        $bproduksi->biaya_sales = $omset * 0.03;
        $bproduksi->biaya_desain = $omset * 0.02;
        $bproduksi->biaya_penanggung_jawab = $omset * 0.03;
        $bproduksi->biaya_pekerjaan = $omset * 0.05;
        $bproduksi->biaya_bpjs = $omset * 0.025;
        $bproduksi->biaya_transportasi = $omset * 0.01;
        $bproduksi->biaya_overhead = $omset * 0.025;
        $bproduksi->biaya_alat_listrik = $omset * 0.02;
        $bproduksi->save();
        $antrian->save();

        return response()->json(['message' => 'Biaya Produksi berhasil disimpan !'], 200);
    }

    public function getMachineByIdBarang($id)
    {
        $mesin = DataKerja::where('barang_id', $id)->pluck('machine_id')->first();
        //jika $mesin tidak kosong, lakukan explode
        $mesinTerpilih = [];
        if($mesin) {
            $mesin = explode(',', $mesin);

            foreach($mesin as $m) {
                $mesinTerpilih[] = Machine::where('id', $m)->first();
                $arrayMesin = [];
                foreach($mesinTerpilih as $m) {
                    $arrayMesin[] = [
                        'mid' => $m->id,
                        'nama' => $m->machine_name
                    ];
                }
            }
        } else {
            $arrayMesin = [];
        }

        return response()->json($arrayMesin);
    }
}
