<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingUser = ref(null);
const form = ref({
    name: '',
    email: '',
    role: 'user',
    balance: 0,
});

onMounted(async () => {
    await loadUsers();
});

const loadUsers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/users');
        users.value = response.data.data;
    } catch (e) {
        console.error('Failed to load users');
    } finally {
        loading.value = false;
    }
};

const openModal = (user = null) => {
    if (user) {
        form.value = {
            name: user.name,
            email: user.email,
            role: user.role,
            balance: user.balance,
        };
        editingUser.value = user;
    } else {
        form.value = {
            name: '',
            email: '',
            role: 'user',
            balance: 0,
        };
        editingUser.value = null;
    }
    showModal.value = true;
};

const saveUser = async () => {
    try {
        if (editingUser.value) {
            await axios.put(`/api/admin/users/${editingUser.value.id}/balance`, { balance: form.value.balance });
            await axios.put(`/api/admin/users/${editingUser.value.id}/role`, { role: form.value.role });
        }
        showModal.value = false;
        loadUsers();
    } catch (e) {
        alert('Failed to update user');
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">User Management</h1>

        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit User</h2>
                <form @submit.prevent="saveUser">
                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Role</label>
                        <select v-model="form.role" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="admin">Admin</option>
                            <option value="reseller">Reseller</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-700 mb-1">Balance (KES)</label>
                        <input v-model="form.balance" type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                            Save Changes
                        </button>
                        <button type="button" @click="showModal = false" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="{
                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                'bg-red-100 text-red-800': user.role === 'admin',
                                'bg-blue-100 text-blue-800': user.role === 'reseller',
                                'bg-gray-100 text-gray-800': user.role === 'user',
                            }">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">KES {{ Number(user.balance).toFixed(2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="openModal(user)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        </td>
                    </tr>
                    <tr v-if="!users.length">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
