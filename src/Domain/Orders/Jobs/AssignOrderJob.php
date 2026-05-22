<?php

namespace Src\Domain\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Models\Entities\Order;

class AssignOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $orderId;


    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(AssignOrderServiceInterface $assignOrderService): void
    {
        $order = Order::find($this->orderId);

        if ($order) {
            $assignOrderService->assign($order);
        }
    }
}
