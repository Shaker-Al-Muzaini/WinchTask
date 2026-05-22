<?php
namespace Src\Domain\Orders\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Src\Domain\Driver\Models\Entities\Driver;

class Order extends Model
{
    protected $fillable = ['lat', 'lng', 'status', 'driver_id'];


    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
