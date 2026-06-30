<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(true);
const showOrderForm = ref(false);
const form = ref({
    service_id: '',
    target_url: '',
    quantity: 100,
});

onMounted(async () => {
    await loadOrders();
});

const loadOrders = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/orders');
        orders.value = response.data.data;
    } catch (e) {
        console.error('Failed to load orders');
    } finally {
        loading.value = false;
    }
};

const placeOrder = async () => {
    try {
        await axios.post('/api/orders', form.value);
        showOrderForm.value = false;
        form.value = { service_id: '', target_url: '', quantity: 100 };
        loadOrders();
    } catch (e) {
        alert(e.response?.data?.error || 'Failed to place order');
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">My Orders</h1>
            <button @click="showOrderForm = !showOrderForm" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                {{ showOrderForm ? 'Cancel' : 'New Order' }}
            </button>
        </div>

        <div v-if="showOrderForm" class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Place New Order</h2>
            <form @submit.prevent="placeOrder">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Service</label>
                        <select v-model="form.service_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">Select Service</option>
                            <option value="1">Instagram Followers</option>
                            <option value="2">TikTok Likes</option>
                            <option value="3">YouTube Views</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Quantity</label>
                        <input v-model="form.quantity" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Target URL</label>
                        <input v-model="form.target_url" type="url" placeholder="https://instagram.com/username" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                </div>
                <button type="submit" class="mt-4 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Place Order
                </button>
            </form>
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.service?.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">KES {{ order.price?.toFixed(2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="{
                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                'bg-blue-100 text-blue-800': order.status === 'processing',
                                'bg-green-100 text-green-800': order.status === 'completed',
                                'bg-red-100 text-red-800': order.status === 'cancelled' || order.status === 'refunded',
                            }">
                                {{ order.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                    </tr>
                    <tr v-if="!orders.length">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
