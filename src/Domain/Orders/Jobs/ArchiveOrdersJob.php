<?php

namespace Src\Domain\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Domain\Orders\Models\Entities\OrderArchive;

class ArchiveOrdersJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Order::where('status', 'completed')
            ->where('created_at', '<', now()->subDays(7))
            ->chunkById(500, function ($orders) {

                DB::transaction(function () use ($orders) {
                    OrderArchive::insert($orders->toArray());

                    Order::whereIn('id', $orders->pluck('id'))->delete();
                });
                usleep(500000);
            });
    }
}
