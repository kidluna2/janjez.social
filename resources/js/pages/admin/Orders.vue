<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(true);

onMounted(async () => {
    await loadOrders();
});

const loadOrders = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/orders');
        orders.value = response.data.data;
    } catch (e) {
        console.error('Failed to load orders');
    } finally {
        loading.value = false;
    }
};

const updateStatus = async (order, status) => {
    try {
        await axios.put(`/api/admin/orders/${order.id}/status`, { status, delivered: order.delivered });
        loadOrders();
    } catch (e) {
        alert('Failed to update status');
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Order Management</h1>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty/Del</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.user?.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.service?.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 truncate max-w-xs">{{ order.target_url }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.quantity }} / {{ order.delivered }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">KES {{ order.price?.toFixed(2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select v-model="order.status" @change="updateStatus(order, order.status)" class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button @click="updateStatus(order, 'processing')" class="text-blue-600 hover:text-blue-900 mr-2">Process</button>
                            <button @click="updateStatus(order, 'completed')" class="text-green-600 hover:text-green-900">Complete</button>
                        </td>
                    </tr>
                    <tr v-if="!orders.length">
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
