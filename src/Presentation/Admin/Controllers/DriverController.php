<?php

namespace Src\Presentation\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Presentation\Admin\Requests\StoreDriverRequest;

class DriverController extends Controller
{
    public function store(StoreDriverRequest $request): JsonResponse
    {
        $driver = Driver::create($request->validated());

        return response()->json([
            'message' => 'تم تسجيل الكابتن وتحديد موقعه الجغرافي على الخريطة بنجاح!',
            'driver' => $driver,
        ], 201);
    }
}
