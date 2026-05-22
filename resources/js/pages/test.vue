<template>
    <div class="p-6 bg-slate-50 min-h-screen">
        <div class="flex gap-4 mb-6">
            <button @click="activeTab = 'orders'" :class="tabStyle('orders')">الطلبات النشطة</button>
            <button @click="activeTab = 'drivers'" :class="tabStyle('drivers')">السائقين المسندين</button>
        </div>

        <div v-if="activeTab === 'orders'" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="p-4">رقم الطلب</th>
                    <th class="p-4">الإحداثيات (طول/عرض)</th>
                    <th class="p-4">الإجراءات</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50">
                    <td class="p-4 font-bold text-slate-800">#{{ order.id }}</td>
                    <td class="p-4 text-sm text-slate-500">{{ order.lat }}, {{ order.lng }}</td>
                    <td class="p-4">
                        <button class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">إسناد الطلب</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="p-4">رقم العملية</th>
                    <th class="p-4">اسم السائق</th>
                    <th class="p-4">المسافة</th>
                    <th class="p-4">موقع السائق</th>
                    <th class="p-4">الإجراءات</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                <tr v-for="driver in drivers" :key="driver.id" class="hover:bg-slate-50">
                    <td class="p-4">{{ driver.opId }}</td>
                    <td class="p-4 font-semibold">{{ driver.name }}</td>
                    <td class="p-4 text-blue-600">{{ driver.distance }} كم</td>
                    <td class="p-4 text-xs">{{ driver.lat }}, {{ driver.lng }}</td>
                    <td class="p-4">
                        <button class="text-indigo-600 hover:underline">تعديل العملية</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const activeTab = ref('orders');

const tabStyle = (tab: string) =>
    `px-6 py-2 rounded-full font-medium transition ${activeTab.value === tab ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white border text-slate-600'}`;

// بيانات وهمية للتجربة
const orders = ref([{ id: 101, lat: 31.5, lng: 34.4 }, { id: 102, lat: 31.6, lng: 34.5 }]);
const drivers = ref([{ id: 1, opId: 'OP-500', name: 'أحمد علي', distance: 2.5, lat: 31.4, lng: 34.3 }]);
</script>
