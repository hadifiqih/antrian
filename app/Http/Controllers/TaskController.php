<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Illuminate\Http\Request;
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
        return view('page.task.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
}
