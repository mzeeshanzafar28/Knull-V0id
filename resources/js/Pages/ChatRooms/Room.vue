<script setup>
/*todo:
 - implement reverb web sockets for live chat
 - count how many users are in room, increment|decrement the count when user joins|leaves
 - fail the joining of new member if max_members have joined
 - assign different color bubbles to members, attach member name with bubble
 - show typing
 - show a rules popup when user joins the room
 - implement a "report user" feature, if 5 users report a user: ban him for 20 minutes, on 3 continuous bans, give a 10 days ban
 - implement a "mute user" feature for all users so they can mute anybody.
 */
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
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
        await axios.post(`/chat/${roomId.value}/send`, {
            message: newMessage.value
        });
        newMessage.value = '';
    } catch (err) {
        console.error('Failed to send message:', err);
    }
};

const fetchMessages = async () => {
    try {
        const response = await axios.get(`/chat/${roomId.value}/messages`);
        messages.value = response.data;
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

const scrollToBottom = () => {
    nextTick(() => {
        const container = document.querySelector("#chat-container");
        if (container) {
            requestAnimationFrame(() => {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            });
        }
    });
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
        if (picabo !== hashHex && picabo !== hashHex_1_min_back) {
            //make this alert a bit modern and horror
        alert("You can't break the protocol, you dummy. Join the room from the page...");
        Inertia.get('/chat-rooms', {});
        return;
        }

        window.Echo.private(`chat.${roomId.value}`)
            .listen('.NewChatMessage', (data) => {
                messages.value.push({
                    id: data.message_id,
                    content: data.message,
                    sender: data.sender,
                    created_at: data.timestamp
                });
                scrollToBottom();
            });


        await Promise.all([fetchMessages(), fetchMembers()]);
        scrollToBottom();
    } catch (err) {
        error.value = 'The void has rejected your presence';
        console.error(err);
    } finally {
        loading.value = false;
    }

    // Listen for unload events and remove the user from the room
    window.addEventListener('beforeunload', () => {
        const url = `/chat/${roomId.value}/leave`;
        const formData = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        // Use sendBeacon for reliable delivery during unload
        navigator.sendBeacon(url, formData);
    });

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
                ☠️柩️👻💀
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
            <div class="flex-1 overflow-y-auto mb-6 scrollbar-hide" id="chat-container">
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
                                {{ message.sender }}
                            </div>
                            <div :class="{
                                'text-red-500 font-bold': message.content === 'Dust Cleared by Void',
                                'text-ghost-white': message.content !== 'Dust Cleared by Void'
                            }" class="font-im-fell">
                                {{ message.content }}
                            </div>
                            <div class="text-ghost-white font-im-fell text-xs mt-2">
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
