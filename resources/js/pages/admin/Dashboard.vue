<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('/api/admin/dashboard');
        stats.value = response.data;
    } catch (e) {
        console.error('Failed to load dashboard');
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Users</div>
                <div class="text-3xl font-bold text-blue-600">{{ stats.total_users }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-3xl font-bold text-green-600">{{ stats.total_orders }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Services</div>
                <div class="text-3xl font-bold text-purple-600">{{ stats.total_services }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Revenue</div>
                <div class="text-3xl font-bold text-green-600">KES {{ Number(stats.total_revenue).toFixed(2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Pending Orders</div>
                <div class="text-3xl font-bold text-yellow-600">{{ stats.pending_orders }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Completed Orders</div>
                <div class="text-3xl font-bold text-green-600">{{ stats.completed_orders }}</div>
            </div>
        </div>
    </div>
</template>
