<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Src\Domain\Orders\Jobs\AssignOrderJob;
use Illuminate\Database\Seeder;
use Src\Domain\Orders\Models\Entities\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            $order = Order::create([
                'status' => 'pending',
                'driver_id' => null,
                'lat' => $faker->latitude(24.6500, 24.7900),
                'lng' => $faker->longitude(46.6500, 46.7900),
            ]);
            AssignOrderJob::dispatch($order->id);
        }
    }
}
