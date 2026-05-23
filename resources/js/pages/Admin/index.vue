<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

interface Driver {
    id: number;
    name: string;
    lat: number;
    lng: number;
}

interface Order {
    id: number;
    lat: number;
    lng: number;
    status: string;
    driver?: Driver;
}

const activeTab = ref<'orders' | 'drivers'>('orders');
const orders = ref<Order[]>([]);
const assignedOrders = ref<Order[]>([]);
const isLoading = ref<boolean>(false);
const isSubmitting = ref<number | null>(null);

const tabStyle = (tab: 'orders' | 'drivers') =>
    `px-6 py-2 rounded-full font-medium transition duration-200 ${
        activeTab.value === tab
            ? 'bg-indigo-600 text-white shadow-md'
            : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
    }`;

const switchTab = (tab: 'orders' | 'drivers') => {
    activeTab.value = tab;
    fetchData();
};

// جلب البيانات من الـ API المركزي لطبقة العرض
const fetchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get<Order[]>(`/api/admin/orders?status=${activeTab.value === 'orders' ? 'pending' : 'assigned'}`);
        if (activeTab.value === 'orders') {
            orders.value = response.data;
        } else {
            assignedOrders.value = response.data;
        }
    } catch (error: any) {
        console.error("خطأ أثناء الاتصال بالخادم الداخلي:", error);
        await Swal.fire({
            icon: 'error',
            title: 'فشل جلب البيانات',
            text: 'تأكد من عمل الـ Route Binding لملف مسارات الـ Admin الجديد داخل الـ Provider الخاص بك.',
            confirmButtonText: 'حسناً، سأتحقق',
            confirmButtonColor: '#4f46e5'
        });
    } finally {
        isLoading.value = false;
    }
};

// دالة معالجة الإسناد التلقائي الجغرافي الشاملة
const assignOrder = async (orderId: number) => {
    const confirmResult = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيقوم النظام بحساب أقرب سائق متاح جغرافياً وإسناد هذه الشحنة إليه فوراً.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، قم بالإسناد الآن',
        cancelButtonText: 'إلغاء العملية'
    });

    if (!confirmResult.isConfirmed) return;

    isSubmitting.value = orderId;

    Swal.fire({
        title: 'جاري البحث...',
        text: 'يتم الآن تحديد أقرب سائق متاح جغرافياً للطلب',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await axios.post(`/api/admin/orders/${orderId}/assign`);

        await Swal.fire({
            icon: 'success',
            title: 'تمت العملية بنجاح',
            text: response.data.message,
            timer: 3000,
            showConfirmButton: false
        });

        await fetchData();
    } catch (error: any) {
        console.error("خطأ معالجة الإسناد:", error);
        const serverMessage = error.response?.data?.message || 'فشلت عملية الإسناد، يبدو أنه لا توجد مركبات نقل متاحة حالياً.';

        Swal.fire({
            icon: 'warning',
            title: 'تعذر الإسناد',
            text: serverMessage,
            confirmButtonText: 'مفهوم',
            confirmButtonColor: '#4f46e5'
        });
    } finally {
        isSubmitting.value = null;
    }
};

onMounted(() => {
    fetchData();

    // 🚀 ربط قنوات البث التلقائي لاستقبال التحديثات الحية بدون إعادة تحميل الصفحة
    if (typeof window !== 'undefined' && window.Echo) {

        window.Echo.channel('orders-channel')
            // 1️⃣ عند استقبال طلب جديد من الـ API العام بوضعية pending
            .listen('.order.created', (event: { order: Order }) => {
                console.log('⚡ وصل طلب جديد عبر البث اللحظي:', event.order);
                // إدراج الطلب الجديد مباشرة في أعلى المصفوفة
                orders.value.unshift(event.order);
            })

            .listen('.order.assigned', (event: { order: Order }) => {
                console.log('⚡ تم تحديث وإسناد الطلب بنجاح:', event.order);

                orders.value = orders.value.filter(o => o.id !== event.order.id);

                assignedOrders.value.unshift(event.order);
            });
    }
});
</script>
<template>
    <div class="p-6 bg-slate-50 min-h-screen" dir="rtl">
        <div class="flex gap-4 mb-6">
            <button @click="switchTab('orders')" :class="tabStyle('orders')">
                الطلبات النشطة ({{ orders.length }})
            </button>
            <button @click="switchTab('drivers')" :class="tabStyle('drivers')">
                العمليات المسندة ({{ assignedOrders.length }})
            </button>
        </div>

        <div v-if="isLoading" class="flex flex-col justify-center items-center py-20 text-slate-500 font-medium gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span>جاري تحديث البيانات من النظام المعماري...</span>
        </div>

        <div v-else>
            <div v-if="activeTab === 'orders'" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="p-4 font-semibold">رقم الطلب</th>
                        <th class="p-4 font-semibold">الإحداثيات (عرض / طول)</th>
                        <th class="p-4 font-semibold">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-if="orders.length === 0">
                        <td colspan="3" class="p-8 text-center text-slate-400">لا توجد طلبات نشطة حالياً في لوحة الإدارة.</td>
                    </tr>
                    <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50 transition">
                        <td class="p-4 font-bold text-slate-800">#{{ order.id }}</td>
                        <td class="p-4 text-sm text-slate-500" dir="ltr">
                            {{ order.lat }}, {{ order.lng }}
                        </td>
                        <td class="p-4">
                            <button
                                @click="assignOrder(order.id)"
                                :disabled="isSubmitting === order.id"
                                class="bg-emerald-600 text-white px-5 py-2 rounded-lg hover:bg-emerald-700 disabled:bg-emerald-300 transition font-medium text-sm flex items-center gap-2 shadow-sm"
                            >
                                <span v-if="isSubmitting === order.id" class="inline-block animate-pulse">جاري معالجة الإسناد...</span>
                                <span v-else>إسناد الطلب </span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="p-4 font-semibold">رقم العملية</th>
                        <th class="p-4 font-semibold">اسم السائق المسند</th>
                        <th class="p-4 font-semibold">موقع السائق الحالي</th>
                        <th class="p-4 font-semibold">الحالة الكلية</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-if="assignedOrders.length === 0">
                        <td colspan="4" class="p-8 text-center text-slate-400">لا توجد عمليات مسندة إلى سائقين حالياً.</td>
                    </tr>
                    <tr v-for="item in assignedOrders" :key="item.id" class="hover:bg-slate-50 transition">
                        <td class="p-4 font-bold text-slate-700">OP-{{ item.id }}</td>
                        <td class="p-4 font-semibold text-slate-800">{{ item.driver?.name || 'جاري المزامنة...' }}</td>
                        <td class="p-4 text-xs text-slate-500" dir="ltr">
                            {{ item.driver?.lat }}, {{ item.driver?.lng }}
                        </td>
                        <td class="p-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    تم الإسناد والمتابعة
                                </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

