<?php

namespace App\Models;

use App\Models\Job;
use App\Models\User;
use App\Models\Sales;
use App\Models\Kategori;
use App\Models\SumberPelanggan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iklan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'iklan';

    protected $fillable = [
        'nomor_iklan',
        'tanggal_mulai',
        'tanggal_selesai',
        'user_id',
        'job_id',
        'sales_id',
        'platform',
        'biaya_iklan',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sumber()
    {
        return $this->belongsTo(SumberPelanggan::class, 'platform', 'code_sumber');
    }

}
