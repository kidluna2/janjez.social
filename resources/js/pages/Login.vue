<script setup>
import { ref } from 'vue';
import { router as vueRouter } from '../router';

const form = ref({
    email: '',
    password: '',
});

const loading = ref(false);
const error = ref('');

const login = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.post('/api/login', form.value);
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        vueRouter.push('/dashboard');
    } catch (e) {
        error.value = e.response?.data?.message || 'Login failed';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">🇰🇪 Kenyan SMM Panel</h1>
            <p class="text-center text-gray-600 mb-6">Sign in to manage your social media growth</p>

            <form @submit.prevent="login">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input v-model="form.password" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div v-if="error" class="mb-4 text-red-600 text-sm text-center">{{ error }}</div>
                <button type="submit" :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50">
                    {{ loading ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>
            <p class="text-center text-gray-600 mt-6 text-sm">
                Don't have an account? <router-link to="/register" class="text-blue-600 hover:text-blue-800">Sign up</router-link>
            </p>
        </div>
    </div>
</template>
