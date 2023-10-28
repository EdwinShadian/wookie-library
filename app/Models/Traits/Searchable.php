<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public static function search(
        string $query,
        int $limit,
        int $page,
        string $field
    ): Builder {
        return static::where($field, 'ILIKE', "%$query%")
            ->limit($limit)
            ->offset($limit * ($page - 1));
    }
}
