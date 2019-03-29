<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

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