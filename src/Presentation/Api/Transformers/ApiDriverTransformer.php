<?php

namespace Src\Presentation\Api\Transformers;

use Src\Domain\Driver\Models\Entities\Driver;

class ApiDriverTransformer
{
    public static function transform(Driver $driver): array
    {
        return [
            'id' => $driver->id,
            'name' => $driver->name,
            'latitude' => (float) $driver->lat,
            'longitude' => (float) $driver->lng,
        ];
    }
}
