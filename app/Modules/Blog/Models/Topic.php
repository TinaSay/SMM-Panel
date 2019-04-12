<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'blog_id', 'name', 'description'
    ];

    protected $primaryKey = 'id';

    protected $table = 'topics';

    /**
     * Relationships
     */

    public function posts()
    {
        return $this->hasMany(Post::class, 'topic_id', 'id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
}