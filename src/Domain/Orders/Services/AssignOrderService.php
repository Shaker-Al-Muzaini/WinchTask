<?php

namespace Src\Domain\Orders\Services;

// تأكد من مطابقة هذا الـ Namespace للمكان الفعلي لموديل السائق في مشروعك
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Models\Entities\Order;

class AssignOrderService implements AssignOrderServiceInterface
{
    public function assign(Order $order): bool
    {
        $closestDriver = Driver::whereDoesntHave('orders', function ($query) {
            $query->where('status', 'assigned');
        })
            ->selectRaw('id, name, lat, lng,
                (ST_Distance_Sphere(point(lng, lat), point(?, ?)) / 1000) AS distance', [
                $order->lng, // خط الطول للطلب
                $order->lat,  // خط العرض للطلب
            ])
            ->orderBy('distance')
            ->first();

        // 2. إذا لم يعثر النظام على أي سائق متاح، نرجع false ليعرض الـ Vue تنبيه السويت أليرت
        if (! $closestDriver) {
            return false;
        }

        // 3. إسناد الطلب للسائق وتحديث الحالة بنجاح
        return (bool) $order->update([
            'driver_id' => $closestDriver->id,
            'status' => 'assigned',
        ]);
    }
}
