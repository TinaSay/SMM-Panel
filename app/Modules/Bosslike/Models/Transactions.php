<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $guarded = ['id', 'updated_at'];

    const MONEY_IN = 'in';
    const MONEY_OUT = 'out';

    public function create($user_id, $task_id, $type, $action, $points, $desc, $created = null, $payment_id = null)
    {
        $trans = new Transactions;
        $trans->user_id = $user_id;
        $trans->task_id = $task_id;
        $trans->type = $type;
        $trans->action = $action;
        $trans->points = $points;
        $trans->description = $desc;
        if($created !== null) {
            $trans->created_at = $created;
        }
        $trans->payment_id = $payment_id;
        $trans->save();
    }
}