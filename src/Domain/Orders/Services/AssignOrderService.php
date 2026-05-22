<?php

namespace Src\Domain\Orders\Services;

use Illuminate\Support\Facades\DB;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Events\OrderAssignedEvent;
use Src\Domain\Orders\Models\Entities\Order;

class AssignOrderService implements AssignOrderServiceInterface
{
    public function assign(Order $order): bool
    {
        $isAssigned = DB::transaction(function () use ($order) {

            // 1. قفل سطر الطلب المستهدف (Pessimistic Locking) لحمايته من التزامن العالي
            $lockedOrder = Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedOrder || $lockedOrder->status !== 'pending') {
                return false;
            }

            $closestDriver = Driver::available() // 💡 تم عزل منطق الإتاحة بـ الـ Scope النظيف هنا
                ->selectRaw('id, name, lat, lng,
                    (ST_Distance_Sphere(point(lng, lat), point(?, ?)) / 1000) AS distance', [
                    $lockedOrder->lng,
                    $lockedOrder->lat,
                ])
                ->orderBy('distance')
                ->first();

            if (! $closestDriver) {
                return false;
            }

            return $lockedOrder->update([
                'driver_id' => $closestDriver->id,
                'status' => 'assigned',
            ]);
        });

        if ($isAssigned) {
            event(new OrderAssignedEvent($order));
        }

        return $isAssigned;
    }
}
