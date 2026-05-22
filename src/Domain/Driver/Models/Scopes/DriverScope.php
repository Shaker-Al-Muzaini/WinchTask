<?php

namespace Src\Domain\Driver\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait DriverScope
{
    /**
     * Scope لجلب السائقين المتاحين )
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereDoesntHave('orders', function ($q) {
            $q->where('status', 'assigned');
        });
    }
}
