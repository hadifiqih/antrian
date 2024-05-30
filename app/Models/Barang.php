<?php

namespace App\Models;

use App\Models\Job;
use App\Models\User;
use App\Models\Iklan;
use App\Models\Order;
use App\Models\Kategori;
use App\Models\DataKerja;
use App\Models\RefDesain;
use App\Models\DataAntrian;
use App\Models\DesignQueue;
use App\Models\Documentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'ticket_order',
        'data_antrian_id',
        'customer_id',
        'kategori_id',
        'job_id',
        'user_id',
        'price',
        'qty',
        'note',
        'spesifikasi',
        'acc_desain',
        'desain_queue_id',
        'documentation_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function designQueue()
    {
        return $this->belongsTo(DesignQueue::class, 'design_queue_id', 'id');
    }

    public function desainer()
    {
        return $this->belongsTo(User::class, 'desainer_id', 'id');
    }

    public function refdesain()
    {
        return $this->belongsTo(RefDesain::class);
    }

    public function dataKerja()
    {
        return $this->belongsTo(DataKerja::class, 'ticket_order', 'ticket_order');
    }

    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function antrian()
    {
        return $this->belongsTo(DataAntrian::class, 'ticket_order', 'ticket_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function iklan()
    {
        return $this->belongsTo(Iklan::class);
    }
}
