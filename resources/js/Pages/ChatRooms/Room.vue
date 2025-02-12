<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const { props } = usePage();
const room = ref(props.room);
const roomId = ref(props.roomId);
const members = ref(props.members || []);
const messages = ref([]);
const newMessage = ref('');
const error = ref(null);

// Initialize Laravel Echo using Reverb environment variables
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'default_key', // using Reverb key
    cluster: '', // Provide an empty cluster to satisfy pusher-js requirements
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    disableStats: true,
});

// Function to send a message
const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        // Call our Laravel endpoint; the controller handles encryption
        await axios.post(`/chat/${roomId.value}/send`, {
            message: newMessage.value
        });
        newMessage.value = '';
    } catch (err) {
        console.error('Failed to send message:', err);
    }
};

// Listen for new chat messages
onMounted(() => {
    window.Echo.channel(`chat.${roomId.value}`)
        .listen('NewChatMessage', (event) => {
            messages.value.push({
                sender: event.sender,
                message: event.message,
                timestamp: event.timestamp
            });
        });

    // Optionally, fetch past messages from an endpoint (not shown here)
});
</script>


<template>
    <AppLayout>
    <Head :title="`Chat Room - ${room.name}`" />
    <div class="min-h-screen bg-black text-white p-4">
        <h1 class="text-2xl font-bold">{{ room.name }}</h1>
        <p class="text-gray-400">{{ room.description }}</p>
        <p>Members: {{ members.length }} / {{ room.max_members }}</p>

        <!-- Chat Messages -->
        <div class="chat-box border p-4 h-80 overflow-y-auto my-4">
            <div v-for="(message, index) in messages" :key="index" class="mb-2">
                <strong>{{ message.sender }}:</strong> {{ message.message }}
                <span class="text-gray-500 text-xs">({{ new Date(message.timestamp).toLocaleTimeString() }})</span>
            </div>
        </div>

        <!-- Message Input -->
        <div class="flex space-x-2">
            <input
                v-model="newMessage"
                @keyup.enter="sendMessage"
                class="flex-1 border p-2 bg-gray-800 text-white"
                placeholder="Type your message..."
            />
            <button @click="sendMessage" class="bg-blue-500 px-4 py-2">Send</button>
        </div>
    </div>
</AppLayout>
</template>
