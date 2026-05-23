<?php

namespace Src\Presentation\Api\Controllers;

use Src\Domain\Orders\Repositories\ActiveOrderRepository;
use Src\Domain\Orders\Services\OrderCreationService;
use Src\Presentation\Api\Requests\CreateOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        private OrderCreationService $service,
        private ActiveOrderRepository $repo
    ) {}

    public function store(CreateOrderRequest $request)
    {
        $this->service->execute($request->validated());

        return response()->json(['message' => 'Order queued'], 202);
    }

    public function index()
    {
        return response()->json($this->repo->all());
    }
}
