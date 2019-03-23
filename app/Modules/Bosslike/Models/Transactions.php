<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\Transactions
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property int $task_id
 * @property string|null $action
 * @property string|null $description
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Transactions whereUserId($value)
 * @mixin \Eloquent
 */
class Transactions extends Model
{

    const MONEY_IN = 'in';
    const MONEY_OUT = 'out';

    public function create($user_id, $task_id, $type, $action, $points, $desc)
    {
        $trans = new Transactions;
        $trans->user_id = $user_id;
        $trans->task_id = $task_id;
        $trans->type = $type;
        $trans->action = $action;
        $trans->points = $points;
        $trans->description = $desc;
        $trans->save();
    }
}