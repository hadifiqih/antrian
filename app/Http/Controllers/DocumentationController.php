<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\Documentation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\DataAntrian; // Import the DataAntrian model

class DocumentationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //index
    public function index()
    {
        $dokum = Documentation::all();
        return view('page.dokumentasi.index', compact('dokum'));
    }

    public function indexJson()
    {
        try {
            $barang = Barang::with('antrian')->whereHas('antrian', function ($query) {
                $query->where('status', 1);
            })->where('documentation_id', null)->orderBy('created_at', 'asc')->get(); // Fetch the $barang variable correctly
        
            return Datatables::of($barang)
                ->addIndexColumn()
                ->addColumn('ticket_order', function ($barang) {
                    return $barang->ticket_order;
                })
                ->addColumn('sales', function ($barang) {
                    return $barang->user->sales->sales_name;
                })
                ->addColumn('accdesain', function ($barang) {
                    if (isset($barang->accdesain)) {
                        return '<a class=""><img width="150" src="'. asset('storage/acc-desain/' . $barang->accdesain) .'" class="img-thumbnail"></a>';
                    } else {
                        return 'Tidak ada data';
                    }
                })
                ->addColumn('nama_produk', function ($barang) {
                    if (isset($barang->job->job_name)) {
                        return $barang->job->job_name;
                    } else {
                        return 'Tidak ada data';
                    }
                })
                ->addColumn('action', function ($barang) {
                    return '<a href="'.route('documentation.edit', $barang->id).'" class="btn btn-primary btn-sm">Upload</a>';
                })
                ->rawColumns(['action', 'accdesain'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }        
    }

    public function selesaiJson()
    {
        try {
            $barang = Barang::with('antrian')->where('documentation_id', '!=', null)->orderBy('updated_at', 'desc')->get(); // Fetch the $barang variable correctly
        
            return Datatables::of($barang)
                ->addIndexColumn()
                ->addColumn('ticket_order', function ($barang) {
                    return $barang->ticket_order;
                })
                ->addColumn('sales', function ($barang) {
                    return $barang->user->sales->sales_name;
                })
                ->addColumn('accdesain', function ($barang) {
                    if (isset($barang->accdesain)) {
                        return '<a class=""><img width="150" src="'. asset($barang->accdesain) .'" class="img-thumbnail"></a>';
                    } else {
                        return 'Tidak ada data';
                    }
                })
                ->addColumn('nama_produk', function ($barang) {
                    if (isset($barang->job->job_name)) {
                        return $barang->job->job_name;
                    } else {
                        return 'Tidak ada data';
                    }
                })
                ->addColumn('action', function ($barang) {
                    return '<button onclick="tampilDokumentasi('.$barang->documentation_id.')" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>';
                })
                ->rawColumns(['action', 'accdesain'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $dokum = Documentation::find($id);
        return response()->json(['data' => $dokum, 'status' => 200]);
    }

    public function edit($id)
    {
        $barang = Barang::find($id);
        
        return view('page.dokumentasi.edit', compact('barang'));
    }

    public function uploadGambar(Request $request)
    {
        $this->validate(request(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:51200',
        ]);

        $file = request()->file('file');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = 'dokumentasi/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($file));

        $dokum = new Documentation;
        $dokum->filename = $filename;
        $dokum->path_file = $path;
        $dokum->type_file = $file->getClientMimeType();
        $dokum->job_id = request('job_id');
        $dokum->save();

        $barang = Barang::find(request('barang_id'));
        $barang->documentation_id = $dokum->id;
        $barang->save();

        //cek apakah file sudah tersimpan dalam folder
        if(Storage::disk('public')->exists($path)){
            return response()->json(['message' => 'Berhasil diunggah!'], 200);
        }else{
            return response()->json(['message' => 'Gagal diunggah!'], 500);
        }
    }

    public function galleryDokumentasi(Request $request)
    {
        if($request != null && $request->get('produk') != null){
            $jenisProduk = Job::all();
            $selectedProduk = $request->get('produk');
            $barang = Barang::with('job', 'user.sales', 'documentation')->orderBy('updated_at', 'desc')->where('job_id', $selectedProduk)->where('documentation_id', '!=', null)->paginate(32);
            return view('page.dokumentasi.gallery', compact('barang', 'jenisProduk', 'selectedProduk'));
        }

        $jenisProduk = Job::all();
        $selectedProduk = '';
        $barang = Barang::with('job', 'user.sales', 'documentation')->orderBy('updated_at', 'desc')->where('documentation_id', '!=', null)->paginate(32);
        return view('page.dokumentasi.gallery', compact('barang', 'jenisProduk', 'selectedProduk'));
    }

    public function uploadGambarProduksi($id)
    {
        $barang = Barang::where('ticket_order', $id)->get();
        $ticket = $id;
        $belumDokumentasi = Barang::where('ticket_order', $id)->where('documentation_id', null)->count();
        if($belumDokumentasi > 0){
            $selesai = false;
        }else{
            $selesai = true;
        }
        return view('page.dokumentasi.upload-dokumentasi-produksi', compact('barang', 'ticket', 'selesai'));
    }

    public function hapusFileSampah()
    {
        // 1. Ambil daftar file dari direktori hosting
        $files = Storage::disk('dokumentasi')->files(); // Ganti 'your-disk' dengan disk yang Anda gunakan

        // 2. Ambil daftar nama file dari database
        $dbFiles = DB::table('documentations')->pluck('filename')->toArray(); // Ganti 'your_table' dan 'file_name' 

        // 3. Looping file dari direktori hosting
        foreach ($files as $file) {
            // 4. Jika file tidak ada di database, hapus file
            if (!in_array(basename($file), $dbFiles)) {
                Storage::disk('dokumentasi')->delete($file); // Ganti 'your-disk' dengan disk yang Anda gunakan
            }
        }

        return response()->json(['message' => 'Berhasil menghapus file sampah!']);
    }
}
