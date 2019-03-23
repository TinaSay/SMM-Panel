<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\TaskDone
 *
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Bosslike\Models\Task $task
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\TaskDone whereUserId($value)
 * @mixin \Eloquent
 */
class TaskDone extends Model
{
    protected $table = 'tasks_done';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    const DONE_TASK = 1;
    const HIDDEN_TASK = 2;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}