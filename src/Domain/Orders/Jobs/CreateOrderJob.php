<?php

namespace Src\Domain\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Domain\Orders\Repositories\ActiveOrderRepository;

class CreateOrderJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(private array $data) {}

    public function handle(ActiveOrderRepository $repo): void
    {
        DB::transaction(function () use ($repo) {
            $order = Order::create($this->data);
            $repo->sync($order->toArray());
        });
    }
}
