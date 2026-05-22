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
        'phone',
        'winch_type',
        'is_available',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * علاقة السائق بالطلبات: السائق يمتلك العديد من الطلبات المسندة إليه
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
