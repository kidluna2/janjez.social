<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    amount: 100,
    phone: '',
    payment_method: 'mpesa',
});
const loading = ref(false);
const response = ref(null);

const deposit = async () => {
    loading.value = true;
    response.value = null;
    try {
        const res = await axios.post('/api/deposit', form.value);
        response.value = res.data;
    } catch (e) {
        alert(e.response?.data?.message || 'Deposit failed');
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Add Funds</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form @submit.prevent="deposit">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Payment Method</label>
                    <select v-model="form.payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="mpesa">M-Pesa</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="crypto">Cryptocurrency</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Amount (KES)</label>
                    <input v-model="form.amount" type="number" min="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm text-gray-700 mb-1">Phone Number</label>
                    <input v-model="form.phone" type="text" placeholder="07XXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition disabled:opacity-50">
                    {{ loading ? 'Processing...' : 'Deposit Now' }}
                </button>
            </form>

            <div v-if="response" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800 font-medium">{{ response.message }}</p>
                <p v-if="response.data?.mpesa_response" class="text-sm text-gray-600 mt-2">
                    Check your phone for M-Pesa prompt
                </p>
            </div>
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-800 mb-2">M-Pesa Payment</h3>
            <p class="text-sm text-blue-700">
                Select M-Pesa as payment method and enter your phone number to receive an STK Push prompt. 
                Enter your M-Pesa PIN to complete the deposit.
            </p>
        </div>
    </div>
</template>
