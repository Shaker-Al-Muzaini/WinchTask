<?php

namespace Src\Domain\Orders\Contracts;

use Src\Domain\Orders\Models\Entities\Order;

interface AssignOrderServiceInterface
{
    public function assign(Order $order): bool;
}
