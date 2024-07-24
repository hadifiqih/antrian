<?php

namespace App\Models;

use App\Models\TaskModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [
        'task_id',
        'file_name',
        'file_path',
        'created_by',
        'updated_by',
    ];

    public function task()
    {
        return $this->belongsTo(TaskModel::class);
    }
}
