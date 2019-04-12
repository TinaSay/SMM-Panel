<?php

namespace App\Modules\SmmPro\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceQuantity extends Model
{
    protected $fillable = [
        'service_id', 'quantity', 'price'
    ];

    protected $primaryKey = 'id';

    protected $table = 'smmpro_services_quantity';

    public $timestamps = false;
}