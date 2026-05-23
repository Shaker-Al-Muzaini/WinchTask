<?php

namespace Src\Presentation\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\Orders\Events\OrderCreatedEvent;
use Src\Domain\Orders\Jobs\AssignOrderJob;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Presentation\Api\Requests\StoreApiOrderRequest;
use Src\Presentation\Api\Transformers\ApiOrderTransformer;

class ApiOrderController extends Controller
{
    public function store(StoreApiOrderRequest $request): JsonResponse
    {
        $order = Order::create([
            'status' => 'pending',
            'driver_id' => null,
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ]);

        broadcast(new OrderCreatedEvent($order));

        AssignOrderJob::dispatch($order->id);

        return response()->json([
            'success' => true,
            'message' => 'Order received and dispatching assignment algorithm...',
            'data' => ApiOrderTransformer::transform($order),
        ], 201);
    }
}
