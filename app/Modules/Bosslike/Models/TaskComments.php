<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TaskComments extends Model
{

    protected $fillable = [
        'task_id', 'text', 'created_at'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
