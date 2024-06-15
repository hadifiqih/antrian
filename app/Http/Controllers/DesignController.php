<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Order;
use App\Models\Design;
use App\Models\Antrian;
use App\Models\Employee;
use App\Models\DesignQueue;
use Illuminate\Http\Request;
use App\Models\DesignerSkill;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class DesignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexDesain()
    {
        $designs = DesignQueue::all();
        return view('page.antrian-desain.daftar-antrian', compact('designs'));
    }

    public function indexDatatables()
    {
        if(auth()->user()->isSales()){
            $designs = DesignQueue::where('sales_id', auth()->user()->sales->id)->where('status', 0)->orWhere('status', 1)->orderBy('created_at', 'desc')->get();
        }else if(auth()->user()->isDesigner()){
            $designs = DesignQueue::where('designer_id', auth()->user()->id)->where('status', 1)->orderBy('created_at', 'desc')->get();
        }else{
            $designs = DesignQueue::where('status', 1)->orderBy('created_at', 'desc')->get();
        }

        return Datatables::of($designs)
            ->addIndexColumn()
            ->addColumn('desainer', function($design){
                return $design->designer->name ?? '-';
            })
            ->addColumn('sales', function($design){
                return $design->sales->sales_name;
            })
            ->addColumn('job', function($design){
                return $design->job->job_name;
            })
            ->addColumn('ref_desain', function($design){
                $btn = '<div class="btn-group">';
                if($design->ref_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Ref. Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary" href="'.asset('storage/ref-desain/'.$design->ref_desain).'" target="_blank">Ref. Desain</a>';
                }

                if($design->file_cetak == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">File Cetak</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/file-cetak/'.$design->file_cetak).'" target="_blank">File Cetak</a>';
                }

                if($design->acc_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Acc Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/acc-desain/'.$design->acc_desain).'" target="_blank">Acc Desain</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('prioritas', function($design){
                return $design->prioritas == 1 ? '<span id="prioritas" class="badge badge-warning">Prioritas</span>' : 'Biasa';
            })
            ->addColumn('status', function($design){
                $status = $design->statusDesain($design->status);
                return $status;
            })
            ->addColumn('action', function($design){
                if(auth()->user()->isSales()){
                    if($design->status == 0 || $design->status == 1){
                        $btn = '<div class="btn-group">';
                        $btn .= '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-warning">Edit</a>';
                        $btn .= '<button onclick="deleteData('. $design->id .')" class="btn btn-sm btn-danger">Hapus</button>';
                        $btn .= '</div>';
                    }else if($design->status == 2){
                        $btn = '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Selesai</a>';
                    }
                }else if(auth()->user()->isDesigner()){
                    if($design->status == 1){
                        $btn = '<a href="'.route('design.uploadFile', $design->id).'" class="btn btn-sm btn-warning">Upload File Cetak</a>';
                    }else if($design->status == 2){
                        $btn = '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Selesai</a>';
                    }
                }else{
                    $btn = '<button class="btn btn-sm btn-secondary">-</button>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'ref_desain', 'status', 'prioritas'])
            ->make(true);
    }

    public function indexSelesaiDatatables()
    {
        if(auth()->user()->isSales()){
            $designs = DesignQueue::where('sales_id', auth()->user()->sales->id)->orderBy('created_at', 'desc')->where('status', 2)->get();
        }else if(auth()->user()->isDesigner()){
            $designs = DesignQueue::where('designer_id', auth()->user()->id)->where('status', 2)->orderBy('created_at', 'desc')->get();
        }else{
            $designs = DesignQueue::where('status', 2)->orderBy('created_at', 'desc')->get();
        }

        return Datatables::of($designs)
            ->addIndexColumn()
            ->addColumn('desainer', function($design){
                return $design->designer->name ?? '-';
            })
            ->addColumn('sales', function($design){
                return $design->sales->sales_name;
            })
            ->addColumn('job', function($design){
                return $design->job->job_name;
            })
            ->addColumn('ref_desain', function($design){
                $btn = '<div class="btn-group">';
                if($design->ref_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Ref. Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary" href="'.asset('storage/ref-desain/'.$design->ref_desain).'" target="_blank">Ref. Desain</a>';
                }

                if($design->file_cetak == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">File Cetak</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/file-cetak/'.$design->file_cetak).'" target="_blank">File Cetak</a>';
                }

                if($design->acc_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Acc Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/acc-desain/'.$design->acc_desain).'" target="_blank">Acc Desain</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('prioritas', function($design){
                return $design->prioritas == 1 ? '<span id="prioritas" class="badge badge-warning">Prioritas</span>' : 'Biasa';
            })
            ->addColumn('status', function($design){
                $status = $design->statusDesain($design->status);
                return $status;
            })
            ->addColumn('action', function($design){
                if(auth()->user()->isSales()){
                    if($design->status == 0 || $design->status == 1){
                        $btn = '<div class="btn-group">';
                        $btn .= '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-warning">Edit</a>';
                        $btn .= '<button onclick="deleteData('. $design->id .')" class="btn btn-sm btn-danger">Hapus</button>';
                        $btn .= '</div>';
                    }elseif($design->status == 2){
                        $btn = '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Selesai</a>';
                    }
                }elseif(auth()->user()->isDesigner()){
                    if($design->status == 1){
                        $btn = '<a href="'.route('design.uploadDesain', $design->id).'" class="btn btn-sm btn-warning">Upload File Cetak</a>';
                    }elseif($design->status == 2){
                        $btn = '<a href="'.route('design.editDesain', $design->id).'" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Selesai</a>';
                    }else{
                        $btn = '<button class="btn btn-sm btn-secondary">Tidak Ditugaskan</button>';
                    }
                }else{
                    $btn = '<button class="btn btn-sm btn-secondary">Bukan Desainer</button>';
                }
                return $btn;
            })
            ->rawColumns(['ref_desain', 'status', 'prioritas', 'action'])
            ->make(true);
    }

    public function uploadFile($id)
    {
        $design = DesignQueue::find($id);
        return view('page.antrian-desain.upload-file-cetak', compact('design'));
    }

    public function downloadFile($id)
    {
        $design = DesignQueue::find($id);
        $path = 'storage/file-cetak/' . $design->file_cetak;
        return response()->download($path);
    }

    public function simpanFile(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'fileCetak' => 'nullable|max:51200',
            'linkFile' => 'nullable',
        ]);

        if($validated->fails()){
            return redirect()->back()->withErrors($validated)->withInput();
        }
        
        $design = DesignQueue::find($id);
        $design->simpanFileCetak($request);
        return redirect()->route('design.indexDesain')->with('success', 'File berhasil diupload!');
    }

    public function daftarPenugasan()
    {
        return view('page.antrian-desain.daftar-penugasan');
    }

    public function indexPenugasanDatatables()
    {
        $designs = DesignQueue::with('sales', 'job')->where('status', 0)->orderBy('created_at', 'desc')->get();

        return Datatables::of($designs)
            ->addIndexColumn()
            ->addColumn('judul', function($design){
                return $design->judul;
            })
            ->addColumn('sales', function($design){
                return $design->sales->sales_name;
            })
            ->addColumn('job', function($design){
                return $design->job->job_name;
            })
            ->addColumn('note', function($design){
                return $design->note;
            })
            ->addColumn('prioritas', function($design){
                return $design->prioritas == 1 ? '<span id="prioritas" class="badge badge-warning">Prioritas</span>' : 'Biasa';
            })
            ->addColumn('status', function($design){
                $status = $design->statusDesain($design->status);
                return $status;
            })
            ->addColumn('action', function($design){
                $btn = '<div class="btn-group">';
                $btn .= '<a href="'.route('design.showPenugasan', $design->id).'" class="btn btn-sm btn-outline-primary">Tugaskan</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action', 'status', 'prioritas'])
            ->make(true);
    }

    public function indexPenugasanSelesaiDatatables()
    {
        $designs = DesignQueue::with('designer', 'sales', 'job')->where('status', 2)->orderBy('created_at', 'desc')->get();

        return Datatables::of($designs)
            ->addIndexColumn()
            ->addColumn('judul', function($design){
                return $design->judul;
            })
            ->addColumn('desainer', function($design){
                return $design->designer->name ?? '-';
            })
            ->addColumn('sales', function($design){
                return $design->sales->sales_name;
            })
            ->addColumn('job', function($design){
                return $design->job->job_name;
            })
            ->addColumn('ref_desain', function($design){
                $text = $design->file_cetak;
                $linkPattern = '/https?:\/\/(?:www\.)?\w+(?:\.\w+)+\S*/';
                $fileNamePattern = '/[a-zA-Z0-9_\-.]+\.(cdr|jpeg|jpg|txt|pdf|docx)/';

                $btn = '<div class="btn-group">';
                if($design->ref_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Ref. Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary" href="'.asset('storage/ref-desain/'.$design->ref_desain).'" target="_blank">Ref. Desain</a>';
                }

                if($design->file_cetak == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">File Cetak</span>';
                }else{
                    $isLink = preg_match($linkPattern, $text);
                    $isFileName = preg_match($fileNamePattern, $text);

                    if($isLink){
                        $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.$design->file_cetak.'" target="_blank">File Cetak</a>';
                    }else{
                        $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/file-cetak/'.$design->file_cetak).'" target="_blank" download="'.$design->file_cetak.'">File Cetak</a>';
                    }
                }

                if($design->acc_desain == null){
                    $btn .= '<span class="btn btn-sm btn-secondary disabled">Acc Desain</span>';
                }else{
                    $btn .= '<a class="btn btn-sm btn-primary p-1" href="'.asset('storage/acc-desain/'.$design->acc_desain).'" target="_blank">Acc Desain</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->addColumn('note', function($design){
                return $design->note;
            })
            ->addColumn('prioritas', function($design){
                return $design->prioritas == 1 ? '<span id="prioritas" class="badge badge-warning">Prioritas</span>' : 'Biasa';
            })
            ->addColumn('status', function($design){
                $status = $design->statusDesain($design->status);
                return $status;
            })
            ->addColumn('mulai_penugasan', function($design){
                return $design->start_design;
            })
            ->addColumn('diselesaikan', function($design){
                return $design->end_design;
            })
            ->addColumn('action', function($design){
                return '<button class="btn btn-sm btn-success">Desain Selesai</button>';
            })
            ->rawColumns(['action', 'status', 'prioritas', 'ref_desain'])
            ->make(true);
    }

    public function editDesain($id)
    {
        $design = DesignQueue::find($id);
        $jobs = Job::all();
        return view('page.antrian-desain.edit-desain', compact('design', 'jobs'));
    }

    public function showPenugasan($id)
    {
        $design = DesignQueue::find($id);
        $employees = User::where('can_design', 1)->get();
        return view('page.antrian-desain.tugaskan-desain', compact('design', 'employees'));
    }

    public function pilihDesainer(string $desainer, string $queueId)
    {
        $design = DesignQueue::find($queueId);
        $design->designer_id = $desainer;
        $design->start_design = now();
        $design->status = 1;
        $design->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Desainer berhasil ditugaskan!'
        ]);
    }

    public function penugasanOtomatis(string $id)
    {
        $design = DesignQueue::find($id);
        $produk = $design->job_id;

        $designer = DesignerSkill::where('job_id', $produk)->get();

        if($designer->count() == 0){
            return response()->json([
                'status' => 404,
                'message' => 'Desainer tidak ditemukan!'
            ]);
        }else{
            if($designer->count() == 1){
                return response()->json([
                    'status' => 200,
                    'message' => 'Desainer ditemukan!',
                    'desainer_id' => $designer[0]->designer_id,
                    'name' => $designer[0]->designer->name ?? '-'
                ]);
            }else{
                $selectedDesigner = null;
                $minQueueCount = PHP_INT_MAX;

                foreach ($designer as $des) {
                    $queueCount = DesignQueue::where('designer_id', $des->designer_id)->where('status', 1)->count();
                    
                    if ($queueCount == 0) {
                        $selectedDesigner = $des->designer_id;
                        break;
                    } else {
                        if ($queueCount < $minQueueCount) {
                            $minQueueCount = $queueCount;
                            $selectedDesigner = $des->designer_id;
                        }
                    }
                }
    
                if ($selectedDesigner !== null) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Desainer ditemukan!',
                        'id' => $selectedDesigner,
                        'name' => User::find($selectedDesigner)->name
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Terjadi kesalahan saat menugaskan desainer!'
                    ]);
                }
            }
        }
    }

    public function tambahDesain()
    {
        return view('page.antrian-desain.tambah-desain');
    }

    public function storeAddDesain(Request $request)
    {
        $rules = [
            'judul' => 'required',
            'sales_id' => 'required',
            'job_id' => 'required',
            'note' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $design = new DesignQueue;
        $design->simpanTambahDesain($request);

        return redirect()->route('design.indexDesain')->with('success', 'Desain berhasil ditambahkan');
    }

    public function updateDesain(Request $request, $id)
    {
        $design = DesignQueue::find($id);
        if($request->hasFile('ref_desain')){
            $design->hapusRefDesainDanFile();
            $design->simpanEditDesain($request);
        }else{
            $design->simpanEditDesain($request);
        }

        return redirect()->route('design.indexDesain')->with('success', 'Desain berhasil diubah');
    }

    public function deleteDesain($id)
    {
        $design = DesignQueue::find($id);
        $design->hapusRefDesainDanFile();
        $design->delete();

        return redirect()->route('design.indexDesain')->with('success', 'Desain berhasil dihapus');
    }

    ///---------------------------------------

    public function simpanFileProduksi(Request $request)
    {
        $rules = [
            'fileCetak' => 'required|max:204800',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $file = $request->file('fileCetak');
        $nama_file = time()."_".$file->getClientOriginalName();
        $path = 'file-jadi/' . $nama_file;
        Storage::disk('public')->put($path, $file->get());

        $design = new Design;
        $design->ticket_order = $request->ticketOrder;
        $design->title = $request->judulFile;
        $design->filename = $nama_file;
        $design->employee_id = $request->desainer;
        $design->save();

        $antrian = Antrian::where('ticket_order', $request->ticketOrder)->first();
        $antrian->design_id = $design->id;
        $antrian->is_aman = 1;
        $antrian->save();

        return redirect()->route('estimator.index')->with('success', 'File berhasil diupload');

    }

    public function getSkillById($id)
    {
        $hasSkill = DesignerSkill::where('designer_id', $id)->get();

        $savedSkill = [];
        foreach($hasSkill as $skill){
            //for response job_id dan job_name
            $job = Job::find($skill->job_id);
            $savedSkill[] = [
                'job_id' => $job->id,
                'job_name' => $job->job_name,
            ];
        }

        return response()->json($savedSkill);
    }

    public function addSkill(Request $request)
    {
        $arraySkill = $request->skill;
        $designer = $request->user_id;

        foreach($arraySkill as $skill){
            $checkSkill = DesignerSkill::where('designer_id', $designer)->where('job_id', $skill)->first();
            if($checkSkill){
                continue;
            }else{
                $newSkill = new DesignerSkill;
                $newSkill->designer_id = $designer;
                $newSkill->job_id = $skill;
                $newSkill->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Skill berhasil ditambahkan!'
        ]);
    }
}
