<?php

namespace Src\Domain\Driver\Models\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Domain\Orders\Models\Entities\Order;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }
}
