<?php

namespace Src\Presentation\Admin\Controllers;

use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Jobs\AssignOrderJob;
use Src\Domain\Orders\Models\Entities\Order;
use Src\Presentation\Admin\Requests\AssignOrderRequest;

class OrderAssignmentController extends Controller
{
    protected AssignOrderServiceInterface $assignmentService;

    public function __construct(AssignOrderServiceInterface $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }


    public function renderDashboard(): Response
    {
        return Inertia::render('Admin/index', [
            'initialOrders' => Order::pending()->latest()->get(),
            'initialAssigned' => Order::assigned()->with('driver')->latest()->get(),
        ]);
    }


    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        if ($status === 'pending') {
            $orders = Order::pending()->latest()->get();

            return response()->json($orders);
        }

        // جلب الطلبات المسندة مع بيانات السائق بالاعتماد على الـ Scope المخصص
        $assignedOrders = Order::assigned()
            ->with('driver')
            ->latest()
            ->get();

        return response()->json($assignedOrders);
    }

    /**
     * الإسناد اليدوي (عند ضغط الـ Admin على الزر الأخضر في لوحة التحكم)
     */
    public function assign(AssignOrderRequest $request, $id): JsonResponse
    {
        // التأكد من أن الطلب المستهدف معلق وموجود فعلياً باستخدام الـ Scope
        $order = Order::pending()->findOrFail($id);

        $success = $this->assignmentService->assign($order);

        if (! $success) {
            return response()->json([
                'message' => 'نعتذر، لا يوجد أي سائق متاح حالياً في المحيط الجغرافي لتلبية الطلب!',
            ], 422);
        }

        return response()->json([
            'message' => 'ممتاز! تم إسناد الطلب بنجاح لأقرب سائق متاح جغرافيّاً.',
        ]);
    }

    /**
     * 🛠️ دالة محاكاة وصول طلبات حية (Simulation Endpoint)
     * تستخدم لتجربة النظام الأوتوماتيكي بالكامل دون الحاجة للـ Tinker أو الـ Seeders مسبقاً
     */
    public function simulateIncomingOrder(Request $request): JsonResponse
    {
        $faker = Factory::create('ar_SA');

        $order = Order::create([
            'status' => 'pending',
            'driver_id' => null,
            'lat' => $faker->latitude(24.6500, 24.7900),
            'lng' => $faker->longitude(46.6500, 46.7900),
        ]);

        AssignOrderJob::dispatch($order->id);

        return response()->json(data: [
            'message' => 'تمت محاكاة وصول طلب جديد بنجاح! جاري تشغيل محرك البحث الجغرافي التلقائي وبث النتيجة...',
            'order' => $order,
        ]);
    }
}
