<?php

namespace Src\Presentation\Api\Transformers;

use Src\Domain\Orders\Models\Entities\Order;

class ApiOrderTransformer
{
    public static function transform(Order $order): array
    {
        return [
            'id'         => $order->id,
            'status'     => $order->status,
            'latitude'   => (float) $order->lat,
            'longitude'  => (float) $order->lng,
            'created_at' => $order->created_at->toIso8601String(),
        ];
    }
}
