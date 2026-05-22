<?php

namespace Src\Presentation\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Presentation\Admin\Requests\AssignOrderRequest;

class OrderAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(AssignOrderServiceInterface $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function renderDashboard(): Response
    {
        return Inertia::render('Admin/index', [
            'initialOrders' => Order::where('status', 'pending')->latest()->get(),
            'initialAssigned' => Order::where('status', 'assigned')->with('driver')->latest()->get(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        if ($status === 'pending') {
            $orders = Order::where('status', 'pending')->latest()->get();

            return response()->json($orders);
        }

        // (Eager Loading)
        $assignedOrders = Order::where('status', 'assigned')
            ->with('driver')
            ->latest()
            ->get();

        return response()->json($assignedOrders);
    }

    public function assign(AssignOrderRequest $request, $id): JsonResponse
    {
        $order = Order::where('status', 'pending')->findOrFail($id);

        $success = $this->assignmentService->assign($order);

        if (! $success) {
            return response()->json(['message' => 'نعتذر، لا يوجد أي سائق متاح حالياً لتلبية الطلب!'], 422);
        }

        return response()->json(['message' => 'ممتاز! تم إسناد الطلب بنجاح لأقرب سائق متاح.']);
    }
}
