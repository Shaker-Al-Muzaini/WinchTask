<?php

namespace Src\Domain\Orders\Services;

use Illuminate\Support\Facades\DB;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Events\OrderAssignedEvent;
use Src\Domain\Orders\Models\Entities\Order;

class AssignOrderService implements AssignOrderServiceInterface
{
    public function assign(int|Order $orderId, ?int $driverId = null): bool
    {
        $isAssigned = DB::transaction(function () use ($orderId, $driverId, &$order) {

            $order = Order::where('id', $orderId)->lockForUpdate()->first();

            if (! $order || $order->status !== 'pending') {
                return false;
            }

            $driverQuery = Driver::query();

            if ($driverId) {
                $driverQuery->where('id', $driverId)
                    ->whereDoesntHave('orders', function ($query) {
                        $query->whereIn('status', ['assigned', 'in_progress', 'picked_up']);
                    });
            } else {
                $driverQuery->available()
                    ->selectRaw('*, (ST_Distance_Sphere(point(lng, lat), point(?, ?)) / 1000) AS distance', [
                        $order->lng,
                        $order->lat,
                    ])
                    ->orderBy('distance');
            }

            $targetDriver = $driverQuery->lockForUpdate()->first();

            if (! $targetDriver) {
                return false;
            }

            return $order->update([
                'driver_id' => $targetDriver->id,
                'status' => 'assigned',
            ]);
        });

        if ($isAssigned && $order) {
            event(new OrderAssignedEvent($order));
        }

        return $isAssigned;
    }
}
