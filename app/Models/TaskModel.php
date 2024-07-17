<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'nama_task',
        'rincian',
        'hasil',
        'batas_waktu',
        'akhir_batas_waktu',
        'status',
        'sales_id',
        'priority',
        'category',
        'gps_location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
