<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const tickets = ref([]);
const showForm = ref(false);
const form = ref({
    subject: '',
    message: '',
    priority: 'medium',
});
const loading = ref(true);

onMounted(async () => {
    await loadTickets();
});

const loadTickets = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/tickets');
        tickets.value = response.data.data;
    } catch (e) {
        console.error('Failed to load tickets');
    } finally {
        loading.value = false;
    }
};

const submitTicket = async () => {
    try {
        await axios.post('/api/tickets', form.value);
        showForm.value = false;
        form.value = { subject: '', message: '', priority: 'medium' };
        loadTickets();
    } catch (e) {
        alert('Failed to create ticket');
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Support Tickets</h1>
            <button @click="showForm = !showForm" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                {{ showForm ? 'Cancel' : 'New Ticket' }}
            </button>
        </div>

        <div v-if="showForm" class="bg-white rounded-lg shadow p-6 mb-6">
            <form @submit.prevent="submitTicket">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Subject</label>
                    <input v-model="form.subject" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Priority</label>
                    <select v-model="form.priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 mb-1">Message</label>
                    <textarea v-model="form.message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required></textarea>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Submit Ticket
                </button>
            </form>
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">Loading...</div>

        <div v-else class="space-y-4">
            <div v-for="ticket in tickets" :key="ticket.id" class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-900">{{ ticket.subject }}</span>
                    <span :class="{
                        'px-2 py-1 text-xs font-medium rounded-full': true,
                        'bg-yellow-100 text-yellow-800': ticket.status === 'open',
                        'bg-blue-100 text-blue-800': ticket.status === 'in_progress',
                        'bg-green-100 text-green-800': ticket.status === 'resolved',
                        'bg-gray-100 text-gray-800': ticket.status === 'closed',
                    }">
                        {{ ticket.status.replace('_', ' ') }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">{{ ticket.message }}</p>
                <div class="text-xs text-gray-400">#{{ ticket.ticket_number }} | {{ new Date(ticket.created_at).toLocaleDateString() }}</div>
            </div>
            <div v-if="!tickets.length" class="text-center text-gray-500 py-12">No tickets found</div>
        </div>
    </div>
</template>
