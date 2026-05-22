<?php

namespace Src\Domain\Orders\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait OrderScope
{
    /**
     * Scope لجلب الطلبات المعلقة فقط
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope لجلب الطلبات التي تم إسنادها لسائق
     */
    public function scopeAssigned(Builder $query): Builder
    {
        return $query->where('status', 'assigned');
    }
}
