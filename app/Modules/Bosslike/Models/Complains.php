<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Complains extends Model
{
    protected $table = 'complains';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }

}