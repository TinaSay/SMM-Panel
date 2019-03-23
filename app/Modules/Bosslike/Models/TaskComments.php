<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\TaskComments
 *
 * @property int $id
 * @property int $task_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Modules\Bosslike\Models\Task $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskComments whereText($value)
 * @mixin \Eloquent
 */
class TaskComments extends Model
{

    protected $fillable = [
        'task_id', 'text', 'created_at'
    ];

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }
}
