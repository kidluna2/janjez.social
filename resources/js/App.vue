<script setup>
import { ref, onMounted } from 'vue';
import { router } from './router';

const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
const showMobileMenu = ref(false);

const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        router.push('/login');
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <router-link to="/dashboard" class="text-xl font-bold text-blue-600">
                            🇰🇪 SMM Panel
                        </router-link>
                        <div class="hidden md:flex ml-10 space-x-8">
                            <router-link to="/services" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Services</router-link>
                            <router-link to="/orders" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Orders</router-link>
                            <router-link to="/deposit" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Deposit</router-link>
                            <router-link to="/tickets" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Support</router-link>
                            <router-link v-if="user.role === 'admin'" to="/admin" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Admin</router-link>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-4">
                        <span class="text-sm text-gray-700">KES {{ user.balance?.toFixed(2) }}</span>
                        <span class="text-sm text-gray-600">{{ user.name }}</span>
                        <button @click="logout" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-8">
            <router-view />
        </main>
    </div>
</template>
