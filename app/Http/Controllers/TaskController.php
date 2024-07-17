<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TaskModel;
use Illuminate\Http\Request;
use App\Models\SumberPelanggan;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('page.task.index');
    }

    public function indexJson()
    {
        $task = TaskModel::with('sales')->get();
        
        return Datatables::of($task)
            ->addIndexColumn()
            ->addColumn('nama_task', function($task){
                return $task->nama_task;
            })
            ->addColumn('status', function($task){
                if($task->status == 'done'){
                    return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-xs btn-success">'.$task->status.'</button>
                    </div>';
                }elseif($task->status == 'pending'){
                    return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-xs btn-warning">'.$task->status.'</button>
                    </div>';
                }elseif($task->status == 'on progress'){
                    return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-xs btn-primary">'.$task->status.'</button>
                    </div>';
                }else{
                    return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-xs btn-danger">'.$task->status.'</button>
                    </div>';
                }
            })
            ->addColumn('keterangan', function($task){
                return $task->rincian;
            })
            ->addColumn('sales', function($task){
                return $task->sales->sales_name;
            })
            ->addColumn('deadline', function($task){
                return date('d-m-Y H:i', strtotime($task->batas_waktu));
            })
            ->addColumn('action', function($task){
                return '<a href="#" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-eye-open"></i> Show</a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $infoPelanggan = SumberPelanggan::all();
        return view('page.task.create', compact('infoPelanggan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_task' => 'required',
        ]);

        $id = auth()->user()->id;

        $task = new TaskModel();
        $task->nama_task = $validated['nama_task'];
        $task->user_id = $id;

        $task->rincian = $request->rincian ?? '';
        $task->hasil = $request->hasil ?? '';
        $task->batas_waktu = $request->batas_waktu ?? '';
        $task->akhir_batas_waktu = $request->akhir_batas_waktu ?? '';
        $task->status = strtolower($request->status);
        $task->priority = strtolower($request->priority) ?? '';
        $task->category = strtolower($request->category) ?? '';
        $task->gps_location = $request->gps_location ?? '';
        $task->customer_id = $request->customer_id ?? '';
        $task->save();

        return redirect()->route('task.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
            $newCustomer->save();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
