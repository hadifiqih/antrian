<?php

namespace App\Models;

use App\Models\Sales;
use App\Models\Customer;
use App\Models\Attachment;
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'task_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
