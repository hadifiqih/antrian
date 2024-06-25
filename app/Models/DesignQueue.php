<?php

namespace App\Models;

use App\Models\Job;
use App\Models\User;
use App\Models\Sales;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesignQueue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'design_queue';

    protected $fillable = [
        'judul',
        'sales_id',
        'job_id',
        'designer_id',
        'file_cetak',
        'file_url',
        'ref_desain',
        'note',
        'prioritas',
        'status'
    ];

    public function dataAntrian()
    {
        return $this->belongsTo(DataAntrian::class, 'data_antrian_id', 'id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id', 'id');
    }

    public function barang()
    {
        return $this->hasOne(Barang::class, 'design_queue_id');
    }

    public function simpanTambahDesain($request)
    {
        if($request->hasFile('ref_desain')){
            $file = $request->file('ref_desain');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'storage/ref-desain';
            $file->move($tujuan_upload,$nama_file);
        }

        $this->judul = $request->judul;
        $this->sales_id = $request->sales_id;
        $this->job_id = $request->job_id;
        $this->ref_desain = $nama_file ?? null;
        $this->note = $request->note;
        $this->prioritas = $request->prioritas == 'ON' ? 1 : 0;
        $this->status = 0;
        $this->save();
    }

    public function simpanEditDesain($request)
    {
        if($request->hasFile('ref_desain')){
            $file = $request->file('ref_desain');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'storage/ref-desain';
            $file->move($tujuan_upload,$nama_file);
        }

        $this->judul = $request->judul;
        $this->sales_id = $request->sales_id;
        $this->job_id = $request->job_id;
        $this->ref_desain = $nama_file ?? $this->ref_desain;
        $this->note = $request->note;
        $this->prioritas = $request->prioritas == 'ON' ? 1 : 0;
        $this->status = 0;
        $this->save();
    }

    public function hapusRefDesainDanFile()
    {
        if($this->ref_desain){
            $file_path = 'storage/ref-desain/'.$this->ref_desain;
            if(file_exists($file_path)){
                unlink($file_path);
            }
            $this->ref_desain = null;
        }

        if($this->file_cetak){
            $file_path = 'storage/file-cetak/'.$this->file_cetak;
            if(file_exists($file_path)){
                unlink($file_path);
            }
            $this->file_cetak = null;
        }

        $this->save();
    }
    
    public function simpanDesainer(Request $request)
    {
        $this->designer_id = $request->designer_id;
        $this->start_design = now();
        $this->status = 1;
        $this->save();
    }

    public function simpanFileCetak(Request $request)
    {
        if ($request->hasFile('fileCetak')){
            $file = $request->file('fileCetak');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('file-cetak', $file, $nama_file);
        }

        // Simpan nama file cetak ke database
        $this->file_cetak = $nama_file ?? null;
        $this->file_url = $request->linkFile;
        $this->end_design = now();
        $this->status = 2;
        $this->save();
    }

    public function statusDesain($status)
    {
        switch ($status) {
            case 0:
                return '<span class="badge badge-secondary">Menunggu</span>';
                break;
            case 1:
                return '<span class="badge badge-primary">Dikerjakan</span>';
                break;
            case 2:
                return '<span class="badge badge-success">Selesai</span>';
                break;
            case 3:
                return '<span class="badge badge-danger">Dibatalkan</span>';
                break;
            default:
                return '<span class="badge badge-danger">Error</span>';
                break;
        }
    }
}
