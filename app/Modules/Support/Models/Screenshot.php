<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    protected $fillable = [
        'feedback_id', 'image'
    ];

    protected $primaryKey = 'id';

    protected $table = 'support_feedback_screenshots';
}