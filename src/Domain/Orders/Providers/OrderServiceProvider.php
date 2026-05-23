<?php

namespace Src\Domain\Orders\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Repositories\ActiveOrderRepository;
use Src\Domain\Orders\Services\AssignOrderService;
use Src\Domain\Orders\Services\OrderCreationService;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActiveOrderRepository::class, function ($app) {
            return new ActiveOrderRepository;
        });

        $this->app->bind(OrderCreationService::class, function ($app) {
            return new OrderCreationService;
        });

        $this->app->bind(AssignOrderServiceInterface::class, AssignOrderService::class);
    }
}
