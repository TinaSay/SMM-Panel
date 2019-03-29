<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\Task
 *
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property string $link
 * @property int $points
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Bosslike\Models\Service $service
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereUserId($value)
 * @mixin \Eloquent
 * @property string $type
 * @property string $picture
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereType($value)
 * @property string|null $post_id
 * @property string|null $post_name
 * @property int $done
 * @property string|null $bosslike_id
 * @property string|null $sng_amounts
 * @property string|null $sng_points
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Bosslike\Models\TaskComments[] $comments
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereBosslikeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task wherePostName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereSngAmounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Task whereSngPoints($value)
 */
class Task extends Model
{

    const INSTAGRAM_USERNAME = 'picstar.uz';
    const INSTAGRAM_PASSWORD = 'secretsecret1234';

    const MONEY_IN = 'in';
    const MONEY_OUT = 'out';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'service_id', 'link', 'picture', 'type', 'points', 'amount', 'done', 'bosslike_id', 'sng_amounts', 'sng_points'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(TaskComments::class, 'task_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function tasks_done()
    {
        return $this->hasMany(TaskDone::class, 'task_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
