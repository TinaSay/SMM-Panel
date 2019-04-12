<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    protected $primaryKey = 'id';

    protected $table = 'blogs';

    /**
     * Relationships
     */

    public function topics()
    {
        return $this->hasMany(Topic::class, 'blog_id', 'id');
    }
}