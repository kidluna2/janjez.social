<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('/api/orders/stats');
        const balanceRes = await axios.get('/api/balance');
        stats.value = {
            ...response.data,
            balance: balanceRes.data.balance
        };
    } catch (e) {
        console.error('Failed to load stats');
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Wallet Balance</div>
                <div class="text-2xl font-bold text-green-600">KES {{ stats.balance?.toFixed(2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-2xl font-bold text-blue-600">{{ stats.total_orders }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Completed Orders</div>
                <div class="text-2xl font-bold text-green-600">{{ stats.completed_orders }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Spent</div>
                <div class="text-2xl font-bold text-red-600">KES {{ stats.total_spent?.toFixed(2) }}</div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <router-link to="/services" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                    <div class="text-2xl mb-2">🛍️</div>
                    <div class="font-medium">Browse Services</div>
                    <div class="text-sm text-gray-500">Instagram, TikTok, YouTube & more</div>
                </router-link>
                <router-link to="/orders" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                    <div class="text-2xl mb-2">📦</div>
                    <div class="font-medium">My Orders</div>
                    <div class="text-sm text-gray-500">Track and manage orders</div>
                </router-link>
                <router-link to="/deposit" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                    <div class="text-2xl mb-2">💳</div>
                    <div class="font-medium">Add Funds</div>
                    <div class="text-sm text-gray-500">M-Pesa, Card, Crypto</div>
                </router-link>
            </div>
        </div>
    </div>
</template>
