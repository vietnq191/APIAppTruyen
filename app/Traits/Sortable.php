<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query, $request)
    {
        $sortables = data_get($this, 'sortables', []);

        $sort = $request['sort_name'] ?? null;

        $direction = $request['sort_type'] ?? 'desc';

        if (
            $sort
            && in_array($sort, $sortables)
            && $direction
            && in_array($direction, ['asc', 'desc'])
        ) {
            return $query->orderBy($sort, $direction);
        }

        return $query;
    }
}
