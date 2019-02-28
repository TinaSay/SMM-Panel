<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SortableTrait
{
    /**
     * Parse the arguments for sorting.
     *
     * @param Request $request
     * @return array
     */
    public function parseSortingArgument(Request $request):array
    {
        if ($request->has('sorting') && $request->sorting) {
            return explode('|', $request->sorting);
        }

        return $this->defaultSorting;
    }

    /**
     * Sorting scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $sorting
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, array $sorting):\Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy($sorting[0], $sorting[1]);
    }
}