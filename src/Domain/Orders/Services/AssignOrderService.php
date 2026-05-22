<?php

namespace Src\Domain\Orders\Services;

use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Models\Entities\Driver;
use Src\Domain\Orders\Models\Entities\Order;

class AssignOrderService implements AssignOrderServiceInterface
{
    public function assign(Order $order): bool
    {
        $closestDriver = Driver::whereDoesntHave('orders', function ($query) {
            $query->where('status', 'assigned');
        })
            ->selectRaw('id, name, lat, lng,
            (ST_Distance_Sphere(point(lng, lat), point(?, ?)) / 1000) AS distance', [$order->lng, $order->lat])
            ->orderBy('distance')
            ->first();

        if (! $closestDriver) {
            return false;
        }

        return $order->update([
            'driver_id' => $closestDriver->id,
            'status' => 'assigned',
        ]);
    }
}
