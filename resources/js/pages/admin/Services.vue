<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const categories = ref([]);
const loading = ref(true);
const showForm = ref(false);
const editing = ref(false);
const form = ref({
    id: null,
    name: '',
    category_id: '',
    price: 0,
    min_quantity: 10,
    max_quantity: 10000,
    description: '',
    status: 'active',
    provider_service_id: '',
    provider_name: '',
});

onMounted(async () => {
    await loadServices();
    await loadCategories();
});

const loadServices = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/services');
        services.value = response.data.data;
    } catch (e) {
        console.error('Failed to load services');
    } finally {
        loading.value = false;
    }
};

const loadCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (e) {
        console.error('Failed to load categories');
    }
};

const openForm = (service = null) => {
    if (service) {
        form.value = { ...service };
        editing.value = true;
    } else {
        form.value = {
            id: null,
            name: '',
            category_id: '',
            price: 0,
            min_quantity: 10,
            max_quantity: 10000,
            description: '',
            status: 'active',
            provider_service_id: '',
            provider_name: '',
        };
        editing.value = false;
    }
    showForm.value = true;
};

const saveService = async () => {
    try {
        const url = editing.value ? `/api/admin/services/${form.value.id}` : '/api/admin/services';
        const method = editing.value ? 'put' : 'post';
        await axios[method](url, form.value);
        showForm.value = false;
        loadServices();
    } catch (e) {
        alert(e.response?.data?.message || 'Failed to save service');
    }
};

const deleteService = async (id) => {
    if (!confirm('Are you sure you want to delete this service?')) return;
    try {
        await axios.delete(`/api/admin/services/${id}`);
        loadServices();
    } catch (e) {
        alert('Failed to delete service');
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Services Management</h1>
            <button @click="openForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                Add Service
            </button>
        </div>

        <div v-if="showForm" class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ editing ? 'Edit' : 'Create' }} Service</h2>
            <form @submit.prevent="saveService">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Service Name</label>
                        <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Category</label>
                        <select v-model="form.category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">Select Category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Price (KES)</label>
                        <input v-model="form.price" type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Min Quantity</label>
                        <input v-model="form.min_quantity" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Max Quantity</label>
                        <input v-model="form.max_quantity" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Save Service
                    </button>
                    <button type="button" @click="showForm = false" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min/Max</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="service in services" :key="service.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ service.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ service.category?.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">KES {{ service.price?.toFixed(2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ service.min_quantity }} / {{ service.max_quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="{
                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                'bg-green-100 text-green-800': service.status === 'active',
                                'bg-red-100 text-red-800': service.status === 'inactive',
                                'bg-yellow-100 text-yellow-800': service.status === 'maintenance',
                            }">
                                {{ service.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="openForm(service)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <button @click="deleteService(service.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!services.length">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No services found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
