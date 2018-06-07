<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NodeBlock
 * @package App
 *
 * @property
 */
class NodeBlock extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMined($query)
    {
        return $query->whereNotNull('hash');
    }
}
