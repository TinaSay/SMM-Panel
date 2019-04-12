<?php

namespace App\Modules\Instashop\Models;


use Illuminate\Database\Eloquent\Model;

class Instashop extends Model
{
    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];

    public function images()
    {
        return $this->hasMany(InstashopImages::class, 'instashop_id', 'id');
    }
}
