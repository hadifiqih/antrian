<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TaskModel;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\SumberPelanggan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        return view('page.task.index');
    }

    public function indexJson()
    {
        $task = TaskModel::with('sales', 'customer')->get();
        
        return Datatables::of($task)
            ->addIndexColumn()
            ->addColumn('nama_task', function($task){
                return $task->nama_task;
            })
            ->addColumn('customer', function($task){
                return $task->customer->nama ?? '-';
            })
            ->addColumn('status', function($task){
                if($task->status == '3'){
                    return '<span class="badge bg-success">Selesai</span>';
                }elseif($task->status == '2'){
                    return '<span class="badge bg-warning">Proses</span>';
                }else{
                    return '<span class="badge bg-danger">Belum Selesai</span>';
                }
            })
            ->addColumn('sales', function($task){
                return $task->user->sales->sales_name;
            })
            ->addColumn('deadline', function($task){
                return date('d-m-Y H:i', strtotime($task->batas_waktu));
            })
            ->addColumn('action', function($task){
                return '<div class="btn-group">
                    <a href="'.route('task.edit', $task->id).'" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteTask('.$task->id.')"><i class="fas fa-trash"></i></button>
                </div>';
            })
            ->addColumn('updated_at', function($task){
                return date('d-m-Y H:i', strtotime($task->updated_at));
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $infoPelanggan = SumberPelanggan::all();
        return view('page.task.create', compact('infoPelanggan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_task' => 'required',
            'status' => 'required',
        ]);

        $id = auth()->user()->id;

        $task = new TaskModel();
        $task->nama_task = $validated['nama_task'];
        $task->user_id = $id;
        $task->rincian = $request->rincian ?? '';
        $task->hasil = $request->hasil ?? '';
        $task->batas_waktu = $request->batasWaktu ?? '';
        $task->akhir_batas_waktu = $request->akhirBatas ?? '';
        $task->status = strtolower($request->status);
        $task->priority = strtolower($request->priority) ?? '';
        $task->category = strtolower($request->category) ?? '';
        $task->customer_id = $request->customerId ?? 0;
        $task->save();

        //simpan lampiran
        if($request->hasFile('lampiran')){
            //jika lampiran lebih dari satu
            if(count($request->file('lampiran')) > 1){
                foreach($request->file('lampiran') as $lampiran){
                    $lampiranName = time() . '.' . $lampiran->getClientOriginalExtension();
                    Storage::disk('public')->put('lampiran/' . $lampiranName, file_get_contents($lampiran));

                    //simpan ke database
                    $lampiran = new Attachment;
                    $lampiran->task_id = $task->id;
                    $lampiran->file_name = $lampiranName;
                    $lampiran->file_path = 'lampiran/' . $lampiranName;
                    $lampiran->save();
                }
            }else{
                $lampiran = $request->file('lampiran');
                $lampiranName = time() . '.' . $lampiran->getClientOriginalExtension();
                Storage::disk('public')->put('lampiran/' . $lampiranName, file_get_contents($lampiran));

                //simpan ke database
                $lampiran = new Attachment;
                $lampiran->task_id = $task->id;
                $lampiran->file_name = $lampiranName;
                $lampiran->file_path = 'lampiran/' . $lampiranName;
                $lampiran->save();
            }
        }

        return redirect()->route('task.index')->with('success', 'Task created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $task = TaskModel::with('attachments', 'customer')->where('id', $id)->first();
        $infoPelanggan = SumberPelanggan::all();
        return view('page.task.edit', compact('task', 'infoPelanggan'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //destroy task
        $task = TaskModel::find($id);
        $task->delete();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }

    public function simpanPelanggan(Request $request)
    {
        try{
            $newCustomer = new Customer;
            $newCustomer->nama = $request->modalNama;
            $newCustomer->telepon = $request->modalTelepon;
            $newCustomer->alamat = $request->modalAlamat;
            $newCustomer->infoPelanggan = $request->modalInfoPelanggan;
            $newCustomer->instansi = $request->modalInstansi;
            $newCustomer->sales_id = $request->salesID;
            $newCustomer->provinsi = $request->provinsi;
            $newCustomer->kota = $request->kota;
            $newCustomer->customer_id = $request->customerID;
            $newCustomer->save();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroyLampiran(Request $request)
    {
        $lampiran = Attachment::find($request->id);
        Storage::disk('public')->delete($lampiran->file_path);
        $lampiran->delete();

        return response()->json(['status' => 'success', 'message' => 'Lampiran berhasil dihapus']);
    }
}
