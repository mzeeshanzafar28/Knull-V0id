<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import { playSound } from '@/utils/sounds';

const { props } = usePage();
const chats = ref([]);
const loading = ref(true);
const unreadMessages = ref({}); // Track unread counts per chat

onMounted(async () => {
    playSound('list_rooms');
    try {
        const response = await axios.post('/inbox');
        chats.value = response.data;

        // Initialize unread counts
        chats.value.forEach(chat => {
            unreadMessages.value[chat.id] = chat.unread_count || 0;
        });

        setupEchoListeners();
    } catch (error) {
        console.error('Failed to fetch private chats:', error);
    } finally {
        loading.value = false;
    }
});

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function setupEchoListeners() {
    // Listen for new private messages
    window.Echo.private(`user.${props.auth.user.id}`)
        .listen('PrivateMessageSent', (data) => {
            if (!data.chat_id) return;

            const chatIndex = chats.value.findIndex(c => c.id === data.chat_id);

            if (chatIndex > -1) {
                // Update last message preview
                chats.value[chatIndex].last_message = {
                    content: data.content,
                    created_at: new Date().toISOString()
                };

                // Increment unread count if not current chat
                if (!isCurrentChat(data.chat_id)) {
                    unreadMessages.value[data.chat_id] = (unreadMessages.value[data.chat_id] || 0) + 1;
                    playSound('message_received');
                }
            }
        });
}

function isCurrentChat(chatId) {
    return window.location.pathname.includes(`/private/message/${chatId}`);
}
</script>

<template>

    <Head title="Private Messages - The Void" />
    <div class="min-h-screen bg-void-black relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/images/static-noise.gif')] opacity-10 z-0"></div>
        <main class="relative z-10 container mx-auto px-4 py-12">
            <h1 class="text-5xl font-creepster text-blood-red mb-8 text-center animate-glitch">
                Cryptic Correspondence
            </h1>

            <div v-if="loading" class="text-ghost-white text-center">
                <div class="animate-pulse">Summoning private communiques...</div>
            </div>

            <div v-else class="max-w-2xl mx-auto">
                <div v-if="chats.length === 0" class="text-white text-center">
                    No whispers from the void... yet
                </div>

                <div v-else class="space-y-4">
                    <div v-for="chat in chats" :key="chat.id" class="bg-black/50 backdrop-blur-sm border-2 border-blood-red/30 rounded-lg p-6
                               hover:border-blood-red transition-all cursor-pointer relative"
                        @click="Inertia.visit(`/private/message/${chat.other_user.name}`)">
                        <!-- Unread notification badge -->
                        <div v-if="unreadMessages[chat.id] > 0" class="absolute -top-2 -right-2 bg-blood-red text-white rounded-full w-6 h-6
                                   flex items-center justify-center text-sm shadow-glow-red animate-pulse">
                            {{ unreadMessages[chat.id] }}
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="font-creepster text-blood-red text-xl">
                                {{ chat.other_user.name }}
                            </div>
                            <div class="text-white text-sm">
                                {{ formatDate(chat.updated_at) }}
                            </div>
                        </div>
                        <div v-if="chat.last_message" class="mt-2 text-white truncate">
                            {{ chat.last_message.content }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.shadow-glow-red {
    box-shadow: 0 0 10px #d10000, 0 0 20px #d10000;
}

.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}
</style>
