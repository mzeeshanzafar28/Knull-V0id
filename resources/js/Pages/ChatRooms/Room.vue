<script setup>
/*todo:
 - implement reverb web sockets for live chat
 - count how many users are in room, increment|decrement the count when user joins|leaves
 - fail the joining of new member if max_members have joined
 - assign different color bubbles to members, attach member name with bubble
 - show typing
 - show a rules popup when user joins the room
 - implement a "report user" feature, if 5 users report a user: ban him for 20 minutes, on 3 continuos bans, give a 10 days ban
 - implement a "mute user" feature for all users so they can mute anybody.
 */

import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const { props } = usePage();
const room = ref(props.room);
const roomId = ref(props.roomId);
const ephemeralKey = ref(props.ephemeralKey || null);

const messages = ref([]);
const newMessage = ref('');
const members = ref([]);
const loading = ref(true);
const error = ref(null);

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        const encryptedMessage = await encryptMessage(newMessage.value);
        await axios.post(`/chat/${roomId.value}/send`, {
            encrypted_message: encryptedMessage,
            iv: 'IV_FROM_ENCRYPTION' // Replace with the actual IV generated during encryption
        });
        newMessage.value = '';
    } catch (err) {
        console.error('Failed to send message to the void:', err);
    }
};

const encryptMessage = async (message) => {
    const response = await axios.post('/api/encrypt', {
        message,
        key: ephemeralKey.value
    });
    return response.data.encrypted_message;
};

const decryptMessage = async (encryptedMessage) => {
    const response = await axios.post('/api/decrypt', {
        encrypted_message: encryptedMessage,
        key: ephemeralKey.value
    });
    return response.data.decrypted_message;
};

const fetchMessages = async () => {
    try {
        const response = await axios.get(`/chat/${roomId.value}/messages`);
        const decryptedMessages = await Promise.all(
            response.data.map(async (msg) => ({
                ...msg,
                content: await decryptMessage(msg.encrypted_message)
            }))
        );
        messages.value = decryptedMessages;
    } catch (err) {
        console.error('Failed to fetch messages from the void:', err);
    }
};

const fetchMembers = async () => {
    try {
        const response = await axios.get(`/chat/${roomId.value}/members`);
        members.value = response.data;
    } catch (err) {
        console.error('Failed to fetch members from the void:', err);
    }
};

onMounted(async () => {
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

        const oneMinuteAgo = new Date(now.getTime() - 60000); // 60000ms = 1 minute
        const hoursAgo = String(oneMinuteAgo.getHours()).padStart(2, '0');
        const minutesAgo = String(oneMinuteAgo.getMinutes()).padStart(2, '0');
        const timeStringAgo = `${hoursAgo}-${minutesAgo}`;

        const dataAgo = encoder.encode(timeStringAgo);
        const hashBufferAgo = await crypto.subtle.digest('SHA-256', dataAgo);
        const hashArrayAgo = Array.from(new Uint8Array(hashBufferAgo));
        const hashHex_1_min_back = hashArrayAgo.map(byte => byte.toString(16).padStart(2, '0')).join('');


        const params = new URLSearchParams(window.location.search);
        const picabo = params.get('picabo');


        if (picabo !== hashHex && picabo !== hashHex_1_min_back) 
    {
        alert("You can't break the protocol, you dummy. Join the room from the page..."); // replace this alert with a nice popup with animations etc
        Inertia.get(`/chat-rooms`, {  });    };
        
        // await Promise.all([fetchMessages(), fetchMembers()]);
    } catch (err) {
        error.value = 'The void has rejected your presence';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head :title="`Chat Room - ${room.name}`" />
    <div class="min-h-screen bg-void-black relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-[url('/images/static-noise.gif')] opacity-10 z-0"></div>

        <!-- Floating Debris -->
        <div class="absolute inset-0 opacity-20 z-0">
            <div v-for="i in 30" :key="i" 
                 class="absolute text-4xl opacity-30 animate-float"
                 :style="{
                     left: `${Math.random() * 100}%`,
                     top: `${Math.random() * 100}%`,
                     animationDelay: `${Math.random() * 5}s`
                 }">
                ☠️⚰️👻💀
            </div>
        </div>

        <main class="relative z-10 container mx-auto px-4 py-12 h-screen flex flex-col">
            <!-- Room Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-creepster text-blood-red">
                        {{ room.name }}
                    </h1>
                    <p class="text-ghost-white font-im-fell">
                        {{ room.description }}
                    </p>
                    <p class="text-ghost-white font-im-fell text-sm">
                        Max Members: {{ room.max_members }} | 
                        Ephemeral: {{ room.is_ephemeral ? 'Yes' : 'No' }}
                        <span v-if="room.is_ephemeral">
                            | Self Destructs After: {{ room.self_destruct_hours }} hour(s)
                        </span>
                    </p>
                </div>
                <div class="text-ghost-white font-im-fell">
                    <span class="text-blood-red">👥</span> {{ members.length }} Souls
                </div>
            </div>

            <!-- Chat Container -->
            <div class="flex-1 overflow-y-auto mb-6 scrollbar-hide">
                <div v-if="loading" class="text-ghost-white text-center">
                    <div class="animate-pulse">Summoning messages from the void...</div>
                </div>

                <div v-else-if="error" class="text-blood-red text-center font-im-fell">
                    {{ error }}
                </div>

                <div v-else class="space-y-4">
                    <div 
                        v-for="message in messages" 
                        :key="message.id"
                        class="flex items-start"
                        :class="{
                            'justify-end': message.sender_id === $page.props.auth.user.id,
                            'justify-start': message.sender_id !== $page.props.auth.user.id
                        }"
                    >
                        <div 
                            class="max-w-[75%] p-4 rounded-lg shadow-void-glow"
                            :class="{
                                'bg-blood-red/20 border border-blood-red': message.sender_id === $page.props.auth.user.id,
                                'bg-black/50 border border-blood-red/30': message.sender_id !== $page.props.auth.user.id
                            }"
                        >
                            <div class="text-ghost-white font-im-fell text-sm mb-2">
                                {{ message.sender_name }}
                            </div>
                            <div class="text-ghost-white font-im-fell">
                                {{ message.content }}
                            </div>
                            <div class="text-ghost-white/50 font-im-fell text-xs mt-2">
                                {{ new Date(message.created_at).toLocaleTimeString() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="bg-black/50 backdrop-blur-sm border border-blood-red/30 rounded-lg p-4">
                <textarea
                    v-model="newMessage"
                    placeholder="Whisper into the void..."
                    class="w-full bg-transparent text-ghost-white font-im-fell placeholder-ghost-white/50 
                           focus:outline-none focus:ring-2 focus:ring-blood-red resize-none"
                    rows="2"
                ></textarea>
                <button
                    @click="sendMessage"
                    class="mt-2 w-full bg-blood-red/20 hover:bg-blood-red/40 border border-blood-red 
                           text-ghost-white py-2 px-4 rounded-lg font-im-fell transition-all
                           hover:shadow-void-glow"
                >
                    Send to the Abyss
                </button>
            </div>
        </main>
    </div>
</template>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
    100% { transform: translateY(0) rotate(360deg); }
}

.animate-float {
    animation: float 10s infinite ease-in-out;
}

.shadow-void-glow {
    box-shadow: 0 0 15px rgba(255, 68, 68, 0.2);
}

.bg-void-black {
    background-color: #0a0a0a;
}

.text-blood-red {
    color: #ff4444;
}

.text-ghost-white {
    color: #f0f0f0;
}

.font-creepster {
    font-family: 'Creepster', cursive;
}

.font-im-fell {
    font-family: 'IM Fell English SC', serif;
}

.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 0.5; }
    50% { opacity: 1; }
    100% { opacity: 0.5; }
}
</style>
