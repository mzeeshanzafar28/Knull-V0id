<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import { playSound } from '@/utils/sounds';
import { Link } from '@inertiajs/vue3';

const { props } = usePage();
const rooms = ref([]);
const loading = ref(true);

const flashError = ref(props.flash.error || '');

const zodRoom = computed(() => rooms.value.find(r => r.name === 'The crown of Zod'));
const otherRooms = computed(() => rooms.value.filter(r => r.name !== 'The crown of Zod'));
const isFaLoaded = ref(false);

onMounted(async () => {
    playSound('list_rooms');

    try {
        const response = await axios.post('/chat-rooms');
        rooms.value = response.data;
    } catch (error) {
        console.error('Failed to fetch rooms from the void:', error);
    } finally {
        loading.value = false;
    }

    // Clear flash error after 5 seconds if present
    if (flashError.value) {
        setTimeout(() => {
            flashError.value = null;
        }, 5000);
    }
});

async function joinRoom(roomId) {
    playSound('join_room');

    try {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const timeString = `${hours}-${minutes}`;

        const encoder = new TextEncoder();
        const data = encoder.encode(timeString);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');

        Inertia.get(`/chat/${roomId}/join`, { picabo: hashHex });
    } catch (error) {
        console.error('The void rejected your entry:', error);
    }
}
</script>

<template>

    <Head title="Chat Rooms - The Void" />
    <div class="min-h-screen bg-void-black relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/images/static-noise.gif')] opacity-10 z-0"></div>
        <div class="absolute inset-0 opacity-20 z-0">
            <div v-for="i in 30" :key="i" class="absolute text-4xl opacity-30 animate-float"
                :style="{ left: `${Math.random() * 100}%`, top: `${Math.random() * 100}%`, animationDelay: `${Math.random() * 5}s` }">
                ☠️⚰️👻💀
            </div>
        </div>
        <main class="relative z-10 container mx-auto px-4 py-12">

            <Link href="/inbox"
                class="fixed top-6 right-6 text-blood-red hover:text-blood-red/80 transition-colors z-50">
            <!-- Always show SVG fallback -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>

            <!-- Show Font Awesome icon only if loaded -->
            <i v-if="isFaLoaded" class="fas fa-inbox text-3xl animate-pulse" aria-hidden="true"></i>
            <span class="sr-only">Private Messages</span>
            </Link>

            <h1 class="text-5xl font-creepster text-blood-red mb-8 text-center animate-glitch">
                Chambers of the Damned
            </h1>

            <!-- Display flash error if available -->
            <div v-if="flashError"
                class="mb-4 p-4 bg-red-100 text-red-700 rounded flex justify-center items-center text-center">
                <p>{{ flashError }}</p>
            </div>


            <!-- Always display rooms even if there was an error -->
            <div v-if="loading" class="text-ghost-white text-center">
                <div class="animate-pulse">Summoning portals from the void...</div>
            </div>
            <div v-else>
                <!-- Special room: The crown of Zod -->
                <div v-if="zodRoom" class="mb-12 flex justify-center">
                    <div class="w-full max-w-2xl bg-black/50 backdrop-blur-sm border-2 border-blood-red rounded-lg p-10
                                shadow-zod-glow hover:shadow-zod-glow-intense transform hover:scale-101 transition-all
                                relative overflow-hidden zodiac-chamber">
                        <div class="absolute inset-0 smoke-effect"></div>
                        <div class="absolute top-1 left-1/2 -translate-x-1/2 text-4xl">
                            👑
                        </div>
                        <div class="relative z-10">
                            <h2 class="text-3xl font-creepster text-blood-red mb-4 text-center">
                                {{ zodRoom.name }}
                            </h2>
                            <p class="font-im-fell text-ghost-white mb-6 text-center">
                                {{ zodRoom.description }}
                            </p>
                            <div class="flex justify-between items-center text-ghost-white font-im-fell text-sm">
                                <span>🔥 Only {{ zodRoom.max_members }} Chosen One</span>
                                <span>⌛ Vanishes in {{ zodRoom.self_destruct_hours }} hours</span>
                            </div>
                            <button @click="joinRoom(zodRoom.id)" class="mt-6 w-full bg-blood-red/30 hover:bg-blood-red/50 border-2 border-blood-red
                                            text-ghost-white py-4 px-8 rounded-lg font-im-fell transition-all
                                            shadow-zod-button-glow hover:shadow-zod-button-glow-intense">
                                Enter Zod's Realm
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Other Rooms -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="room in otherRooms" :key="room.id" class="bg-black/50 backdrop-blur-sm border border-blood-red/30 rounded-lg p-6
                                                   hover:border-blood-red transition-all hover:transform hover:-translate-y-2
                                                   shadow-void relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="text-2xl font-creepster text-blood-red mb-4">{{ room.name }}</h2>
                            <p class="font-im-fell text-ghost-white mb-6">{{ room.description }}</p>
                            <div class="flex justify-between items-center text-ghost-white font-im-fell text-sm">
                                <span>🕳️ {{ room.max_members }} Souls</span>
                                <span v-if="room.is_ephemeral">⏳ Vanishes in {{ room.self_destruct_hours }} hours</span>
                            </div>
                            <button @click="joinRoom(room.id)" class="mt-6 w-full bg-blood-red/20 hover:bg-blood-red/40 border border-blood-red
                                                   text-ghost-white py-3 px-6 rounded-lg font-im-fell transition-all
                                                   hover:shadow-void-glow">
                                Enter Chamber
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
