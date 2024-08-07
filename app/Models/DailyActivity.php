<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sales;
use App\Models\DailyAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_activities';

    protected $fillable = [
        'sales_id',
        'user_id',
        'platform',
        'jenis_konten',
        'jumlah',
        'keterangan',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany(DailyAttachment::class, 'daily_activity_id');
    }
}
