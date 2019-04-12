<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $table = 'funds';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}