<?php
namespace Src\Domain\Orders\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['lat', 'lng', 'status', 'driver_id'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
