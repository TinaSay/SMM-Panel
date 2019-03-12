<?php

namespace App\Modules\Bosslike\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Bosslike\Models\SocialUser
 *
 * @property int $id
 * @property int $social_id
 * @property int $client_id
 * @property string $client_name
 * @property string $avatar
 * @property int $user_id
 * @property string $access_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Bosslike\Models\Social[] $socials
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \App\Modules\Bosslike\Models\Social $social
 * @property string $nickname
 * @property string $avatar
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereNickname($value)
 * @property string $client_name
 * @property int $client_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Bosslike\Models\SocialUser whereClientName($value)
 */
class SocialUser extends Model
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
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
