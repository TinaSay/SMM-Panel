<?php

namespace App\Modules\SmmPro\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property-read \Kalnoy\Nestedset\Collection|\App\Models\Category[] $children
 * @property-read \App\Models\Category|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category d()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereWeight($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property string|null $alias
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereAlias($value)
 * @property string|null $icon
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Category whereIcon($value)
 */
class Category extends Model
{
    use NodeTrait;

    protected $table = 'smmpro_categories';

    protected $fillable = [
        'id', 'name', 'description', 'active', 'alias', 'icon'
    ];

    /**
     * @param $parentId
     * @param $currentParentId
     * @return string
     */
    public static function getCategory($parentId, $currentParentId)
    {
        if ($parentId == $currentParentId) {
            return 'selected';
        } else {
            return '';
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
