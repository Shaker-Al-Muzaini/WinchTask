<?php

namespace Src\Presentation\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Domain\Orders\Services\AssignOrderService;
use Src\Presentation\Api\Requests\AssignOrderRequest;

class AssignOrderController extends Controller
{
    public function __construct(protected AssignOrderService $assignService) {}

    public function __invoke(AssignOrderRequest $request, $id): JsonResponse
    {
        $result = $this->assignService->assign($id, $request->driver_id);

        if (! $result) {
            return response()->json(['message' => 'تعذر إسناد الطلب'], 400);
        }

        return response()->json(['message' => 'تم إسناد الطلب بنجاح']);
    }
}
