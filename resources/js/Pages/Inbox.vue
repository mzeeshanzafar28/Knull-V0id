<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import { playSound } from '@/utils/sounds';

const { props } = usePage();
const chats = ref([]);
const loading = ref(true);

onMounted(async () => {
    playSound('list_rooms');
    try {
        const response = await axios.post('/inbox');
        chats.value = response.data;
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
                               hover:border-blood-red transition-all cursor-pointer"
                        @click="Inertia.visit(`/private/message/${chat.other_user.name}`)">
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
.bg-void-black {
    background: #0a0a0a;
}

.font-creepster {
    font-family: 'Creepster', cursive;
}

.text-blood-red {
    color: #d10000;
}

.text-ghost-white {
    color: #f8f8ff;
}

.animate-glitch {
    animation: glitch 1s linear infinite;
}

@keyframes glitch {

    2%,
    64% {
        transform: translate(2px, 0) skew(0deg);
    }

    4%,
    60% {
        transform: translate(-2px, 0) skew(0deg);
    }

    62% {
        transform: translate(0, 0) skew(5deg);
    }
}

.border-blood-red {
    border-color: #d10000;
}

.hover\:border-blood-red:hover {
    border-color: #ff0000;
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>
