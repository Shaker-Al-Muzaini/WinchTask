<?php

namespace Src\Domain\Orders\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Services\AssignOrderService;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssignOrderServiceInterface::class, AssignOrderService::class);
    }
}
