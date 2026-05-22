<?php

use App\Providers\AppServiceProvider;
use Src\Domain\Orders\Providers\OrderServiceProvider;


return [
    AppServiceProvider::class,
    OrderServiceProvider::class,
];
