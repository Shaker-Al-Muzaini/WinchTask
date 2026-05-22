<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Src\Domain\Driver\Models\Entities\Driver;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');

        for ($i = 0; $i < 10; $i++) {
            Driver::create([
                'name' => 'السائق '.$faker->name('male'),
                'lat' => $faker->latitude(24.6500, 24.7900),
                'lng' => $faker->longitude(46.6500, 46.7900),
            ]);
        }
    }
}
