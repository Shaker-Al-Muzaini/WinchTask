<?php

namespace Src\Presentation\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Presentation\Admin\Requests\StoreDriverRequest;

class DriverController extends Controller
{
    protected AssignOrderServiceInterface $assignmentService;

    public function __construct(AssignOrderServiceInterface $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function store(StoreDriverRequest $request): JsonResponse
    {
        $driver = Driver::create($request->validated());

        $oldestPendingOrder = Order::pending()->orderBy('created_at', 'asc')->first();

        if ($oldestPendingOrder) {
            $this->assignmentService->assign($oldestPendingOrder);
        }

        return response()->json([
            'message' => 'تم تسجيل الكابتن وتحديد موقعه الجغرافي، وتم فحصه وإسناده للطلبات المعلقة إن وجدت!',
            'driver' => $driver,
        ], 201);
    }
}
