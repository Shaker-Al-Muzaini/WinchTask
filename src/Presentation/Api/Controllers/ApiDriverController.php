<?php

namespace Src\Presentation\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Presentation\Api\Requests\StoreApiDriverRequest;
use Src\Presentation\Api\Transformers\ApiDriverTransformer;

class ApiDriverController extends Controller
{
    public function store(StoreApiDriverRequest $request): JsonResponse
    {
        $driver = Driver::create([
            'name' => $request->name,
            'lat' => $request->latitude,
            'lng' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Driver registered successfully inside DDD environment.',
            'data' => ApiDriverTransformer::transform($driver),
        ], 201);
    }
}
