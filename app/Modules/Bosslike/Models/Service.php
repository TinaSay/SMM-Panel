<?php

namespace App\Modules\Bosslike\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\Service
 *
 * @property int $id
 * @property int $social_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Bosslike\Models\Social $social
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Bosslike\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Service whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Service extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function social()
    {
        return $this->belongsTo(Social::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
