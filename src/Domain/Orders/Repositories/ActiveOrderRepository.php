<?php

namespace Src\Domain\Orders\Repositories;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Src\Domain\Orders\Models\Entities\Order;

class ActiveOrderRepository
{
    private const KEY = 'active_orders';

    public function all(): array
    {
        try {
            $orders = Redis::hvals(self::KEY);
            return array_map('json_decode', $orders);
        } catch (\Exception $e) {
            Log::emergency('Redis Read Failed: '.$e->getMessage());

            return Order::where('status', 'active')->get()->toArray();
        }
    }

    public function sync(array $order): void
    {
        Redis::hset(self::KEY, $order['id'], json_encode($order));
    }
}
