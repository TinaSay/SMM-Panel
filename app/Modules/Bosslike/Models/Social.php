<?php

namespace App\Modules\Bosslike\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\Social
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\Social whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Bosslike\Models\Service[] $services
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Bosslike\Models\SocialUser[] $socialUsers
 */
class Social extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
//        return $this->hasMany(Service::class);
        return $this->hasMany(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialUsers()
    {
        return $this->hasMany(SocialUser::class);
    }

}
