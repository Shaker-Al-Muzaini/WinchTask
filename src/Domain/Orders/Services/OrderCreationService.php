<?php

namespace Src\Domain\Orders\Services;

use Src\Domain\Orders\DataTransferObjects\OrderData;
use Src\Domain\Orders\Jobs\CreateOrderJob;

class OrderCreationService
{
    public function execute(array $data): void
    {
        $dto = OrderData::fromRequest($data);

        CreateOrderJob::dispatch($dto->attributes)->onQueue('orders-ingestion');
    }
}
