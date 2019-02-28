<?php

namespace App\Modules\SmmPro\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 *
 * @package App\Models
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $quantity
 * @property string $service_api
 * @property string $service_order_api
 * @property string $type
 * @property string $price
 * @property string $reseller_price
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereResellerPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereServiceApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereServiceOrderApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Service sort($sorting)
 */
class Service extends Model
{
    /**
     * Default service type. Saved as 1.
     */
    const TYPE_DEFAULT = 'Основной';

    protected $table = 'smmpro_services';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Sorting scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $sorting
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, array $sorting)
    {
        return $query->orderBy($sorting[0], $sorting[1]);
    }
}
