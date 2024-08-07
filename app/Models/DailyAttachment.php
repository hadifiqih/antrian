<?php

namespace App\Models;

use App\Models\DailyActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_attachments';

    protected $fillable = [
        'daily_activity_id',
        'file_name',
        'file_path',
    ];

    public function dailyActivity()
    {
        return $this->belongsTo(DailyActivity::class, 'daily_activity_id');
    }
}
