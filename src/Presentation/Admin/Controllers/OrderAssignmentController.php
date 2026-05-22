<?php

namespace Src\Presentation\Admin\Controllers;

use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Orders\Contracts\AssignOrderServiceInterface;
use Src\Domain\Orders\Jobs\AssignOrderJob; // 🚀 استدعاء الـ Job التلقائي المتوافق مع الـ DDD
use Src\Domain\Orders\Models\Entities\Order;
use Src\Presentation\Admin\Requests\AssignOrderRequest;

class OrderAssignmentController extends Controller
{
    protected AssignOrderServiceInterface $assignmentService;

    /**
     * حقن واجهة الخدمة لضمان عزل طبقة البزنس (Dependency Injection)
     */
    public function __construct(AssignOrderServiceInterface $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * عرض لوحة التحكم الرئيسية وتمرير البيانات الابتدائية عبر Inertia
     */
    public function renderDashboard(): Response
    {
        return Inertia::render('Admin/index', [
            'initialOrders' => Order::where('status', 'pending')->latest()->get(),
            'initialAssigned' => Order::where('status', 'assigned')->with('driver')->latest()->get(),
        ]);
    }

    /**
     * جلب قائمة الطلبات بناءً على الحالة (API Endpoint)
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        if ($status === 'pending') {
            $orders = Order::where('status', 'pending')->latest()->get();

            return response()->json($orders);
        }

        // جلب الطلبات المسندة مع بيانات السائق (Eager Loading) لمنع مشكلة الـ N+1 Query
        $assignedOrders = Order::where('status', 'assigned')
            ->with('driver')
            ->latest()
            ->get();

        return response()->json($assignedOrders);
    }

    /**
     * الإسناد اليدوي (خط دفاع العمليات والطوارئ عند ضغط الـ Admin على الزر الأخضر)
     */
    public function assign(AssignOrderRequest $request, $id): JsonResponse
    {
        $order = Order::where('status', 'pending')->findOrFail($id);

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

        // 1. إنشاء طلب عشوائي جديد يحاكي إحداثيات الرياض
        $order = Order::create([
            'status' => 'pending',
            'driver_id' => null,
            'lat' => $faker->latitude(24.6500, 24.7900),
            'lng' => $faker->longitude(46.6500, 46.7900),
        ]);

        // 2. ⚡ إطلاق خوارزمية الإسناد الأوتوماتيكي فوراً في الخلفية عبر الـ Queue Worker
        AssignOrderJob::dispatch($order->id);

        return response()->json([
            'message' => 'تمت محاكاة وصول طلب جديد بنجاح! جاري تشغيل محرك البحث الجغرافي التلقائي وبث النتيجة...',
            'order' => $order,
        ]);
    }
}
