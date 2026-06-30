<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const categories = ref([]);
const loading = ref(true);
const filters = ref({
    category: '',
    search: '',
});

onMounted(async () => {
    try {
        const [svcRes, catRes] = await Promise.all([
            axios.get('/api/services'),
            axios.get('/api/categories'),
        ]);
        services.value = svcRes.data.data;
        categories.value = catRes.data;
    } catch (e) {
        console.error('Failed to load services');
    } finally {
        loading.value = false;
    }
});

const loadServices = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/services', { params: filters.value });
        services.value = response.data.data;
    } catch (e) {
        console.error('Failed to load services');
    } finally {
        loading.value = false;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES'
    }).format(price);
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Services</h1>

        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Category</label>
                    <select v-model="filters.category" @change="loadServices" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Search</label>
                    <input v-model="filters.search" @input="loadServices" type="text" placeholder="Search services..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="service in services" :key="service.id" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ service.category?.name }}</span>
                    <span class="text-green-600 font-bold">{{ formatPrice(service.price) }}</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ service.name }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ service.description }}</p>
                <div class="text-xs text-gray-500 mb-4">
                    Min: {{ service.min_quantity }} / Max: {{ service.max_quantity }}
                </div>
                <router-link to="/orders" class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Order Now
                </router-link>
            </div>
        </div>
    </div>
</template>
