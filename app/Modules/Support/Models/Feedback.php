<?php

namespace App\Modules\Support\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'user_id', 'is_read', 'subject', 'message'
    ];

    protected $primaryKey = 'id';

    protected $table = 'support_feedback';

    /*
     * Relationships
     */

    public function screenshots()
    {
        return $this->hasMany(Screenshot::class, 'feedback_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
