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
 */
class Task extends Model
{

    const INSTAGRAM_USERNAME = 'mari__krasnova';
    const INSTAGRAM_PASSWORD = 'secretsecret1234';
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'service_id', 'link', 'points', 'amount'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
