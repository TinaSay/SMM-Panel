<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'topic_id', 'name', 'preview_text', 'detail_text'
    ];

    protected $primaryKey = 'id';

    protected $table = 'posts';
}