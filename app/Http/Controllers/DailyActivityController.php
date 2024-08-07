<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DailyActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dailyActivity = DailyActivity::all();
        return view('daily-activities.index', compact('dailyActivity'));
    }

    public function marolActivity()
    {
        $activities = DailyActivity::with(['sales', 'user'])->all();
        
        return DataTables::of($activities)
        ->addIndexColumn()
        ->addColumn('sales', function($row){
            return $row->sales->sales_name;
        })
        ->addColumn('tanggal', function($row){
            return $row->created_at->format('d-m-Y');
        })
        ->addColumn('platform', function($row){
            return $row->platform;
        })
        ->addColumn('jenis_konten', function($row){
            return $row->jenis_konten;
        })
        ->addColumn('jumlah', function($row){
            return $row->jumlah;
        })
        ->addColumn('keterangan', function($row){
            return $row->keterangan;
        })
        ->addColumn('action', function($row){
            return '<div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Aksi
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Edit</a>
                    <a class="dropdown-item" href="#">Hapus</a>
                </div>
            </div>
            ';
        })
        ->rawColumns(['action'])
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
        //ubah nama file menjadi time saat ini
        function generateFileName($file)
        {
            return time().'.'.$file->getClientOriginalExtension();
        }

        $validated = $request->validate([
            'sales_id' => 'required',
            'platform' => 'required',
            'jenis_konten' => 'required',
            'jumlah' => 'required',
            'lampiran' => 'required',
        ]);

        $user_id = auth()->user()->id;

        try {
            $dailyActivity = DailyActivity::create([
                'sales_id' => $validated['sales_id'],
                'user_id' => $user_id,
                'platform' => $validated['platform'],
                'jenis_konten' => $validated['jenis_konten'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $request->keterangan ?? null,
            ]);

            foreach ($request->lampiran as $lampiran) {
                //file_name dan file_path diisi dengan hasil generateFileName dan store file
                $dailyActivity->attachments()->create([
                    'file_name' => generateFileName($lampiran),
                    //simpan dalam storage/app/public/attachments/daily-activities
                    Storage::disk('public')->putFileAs('attachments/daily-activities', $lampiran, generateFileName($lampiran)),
                    'file_path' => 'attachments/daily-activities/'.generateFileName($lampiran),
                ]);
            }

            return redirect()->route('sales.summaryReport')->with('success', 'Berhasil menambahkan aktivitas harian');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan aktivitas harian');
        }
    }

    public function storeSalesActivity(Request $request)
    {
        //ubah nama file menjadi time saat ini
        function generateFileName($file)
        {
            return time().'.'.$file->getClientOriginalExtension();
        }

        $validated = $request->validate([
            'platform' => 'required',
            'jenis_konten' => 'required',
            'jumlah' => 'required',
            'lampiran' => 'required',
        ]);

        $user_id = auth()->user()->id;
        $sales_id = auth()->user()->sales->id;

        try {
            $dailyActivity = DailyActivity::create([
                'sales_id' => $sales_id,
                'user_id' => $user_id,
                'platform' => $validated['platform'],
                'jenis_konten' => $validated['jenis_konten'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $request->keterangan ?? null,
            ]);

            foreach ($request->lampiran as $lampiran) {
                //file_name dan file_path diisi dengan hasil generateFileName dan store file
                $dailyActivity->attachments()->create([
                    'file_name' => generateFileName($lampiran),
                    //simpan dalam storage/app/public/attachments/daily-activities
                    Storage::disk('public')->putFileAs('attachments/daily-activities', $lampiran, generateFileName($lampiran)),
                    'file_path' => 'attachments/daily-activities/'.generateFileName($lampiran),
                ]);
            }

            return redirect()->route('sales.summaryReport')->with('success', 'Berhasil menambahkan aktivitas harian');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan aktivitas harian');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyActivity $dailyActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyActivity $dailyActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyActivity $dailyActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyActivity $dailyActivity)
    {
        //
    }
}
