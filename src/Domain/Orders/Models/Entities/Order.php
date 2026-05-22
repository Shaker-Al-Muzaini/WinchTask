<?php

namespace Src\Domain\Orders\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Orders\Models\Scopes\OrderScope;

class Order extends Model
{
    use OrderScope;

    protected $fillable = ['lat', 'lng', 'status', 'driver_id'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
