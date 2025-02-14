<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import { playSound } from '@/utils/sounds';


const { props } = usePage();
const room = ref(props.room);
const roomId = ref(props.roomId);
const ephemeralKey = ref(props.ephemeralKey || null);
const messages = ref([]);
const newMessage = ref('');
const members = ref([]);
const loading = ref(true);
const error = ref(null);

const senderColors = [
    '#ff5733', '#33ff57', '#3357ff', '#ff33a1', '#a133ff', '#33fff6', '#ffc733', '#ff3333'
];

const getSenderColor = (sender) => {
    let hash = 0;
    for (let i = 0; i < sender.length; i++) {
        hash = sender.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % senderColors.length;
    return senderColors[index];
};

const sendMessage = async () => {
    playSound('message_sent');

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
        console.error('Failed to fetch messages:', err);
    }
};

const fetchMembers = async () => {
    try {
        const response = await axios.get(`/chat/${roomId.value}/members`);
        members.value = response.data;
    } catch (err) {
        console.error('Failed to fetch members:', err);
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

const addNewLine = (event) => {
    event.preventDefault();
    newMessage.value += '\n';
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

        const oneMinuteAgo = new Date(now.getTime() - 60000);
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
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div style="font-size: xx-large; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;">
                    <div style="background: #1a1a1a; color: #ff0000; padding: 20px; border: 2px solid #ff0000; font-family: 'Creepster', cursive; text-align: center;">
                        <h1>You can't break the protocol, you dummy!</h1>
                        <p>Join the room from the page...</p>
                        <button style="background: #ff0000; color: #fff; border: none; padding: 10px 20px; cursor: pointer;" onclick="this.parentElement.parentElement.remove()">OK</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            setTimeout(() => {
                Inertia.get('/chat-rooms', {});
            }, 3000);

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
                playSound('message_received');
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
        navigator.sendBeacon(url, formData);
    });
});

setInterval( ()=>{
    fetchMembers();
},2000 );

</script>


<template>
    <Head :title="`Chat Room - ${room.name}`" />
    <div class="chat-room">
        <main class="chat-container">
            <div class="top-left">
                <p>Max Members: {{ room.max_members }}</p>
                <p>Ephemeral: {{ room.is_ephemeral ? 'Yes' : 'No' }}</p>
            </div>
            <div class="top-right">👥 {{ members.length }} Souls</div>
            <div class="room-header">
                <h1>{{ room.name }}</h1>
                <p class="description">{{ room.description }}</p>
            </div>
           <div id="chat-container" class="chat-messages">
                <div v-if="loading" class="loading">Summoning messages from the void...</div>
                <div v-else-if="error" class="error">{{ error }}</div>
                <div v-else>
                    <div v-for="message in messages" :key="message.id" class="message">
                        <div class="sender" :style="{ color: getSenderColor(message.sender) }">
                            {{ message.sender }}
                        </div>
                        <div :class="['message-content', { 'special-message': message.content === 'Dust Cleared by Void' }]">
                            {{ message.content }}
                        </div>
                        <div class="timestamp">{{ new Date(message.created_at).toLocaleTimeString() }}</div>
                    </div>
                </div>
            </div>
            <div class="message-input">
                <textarea
                v-model="newMessage"
                placeholder="Whisper into the void..."
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.shift.enter="addNewLine"
            ></textarea>
                <button class="send-button" @click="sendMessage">Send to the Abyss</button>
            </div>
        </main>
    </div>
</template>

<style scoped>
.chat-room {
    background: #0a0a0a;
    color: #d10000;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Creepster', cursive;
}

.chat-container {
    width: 95%;
    /* max-width: 800px; */
    display: flex;
    flex-direction: column;
    height: 95vh;
    background: #111;
    padding: 20px;
    border: 2px solid #d10000;
    box-shadow: 0 0 20px #d10000;
    position: relative;
}

.top-left {
    position: absolute;
    top: 10px;
    left: 10px;
    color: #ff6666;
    font-size: 0.9rem;
}

.top-right {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 1.2rem;
    color: #ff6666;
}

.room-header {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.description{
    color: bisque;
    font-size: medium;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    background: #222;
    padding: 10px;
    border-radius: 5px;
}

.message {
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 5px;
    background: #1a1a1a;
    color: #fff;
    border-left: 3px solid #d10000;
}

.sender {
    font-weight: bold;
    color: #ff4444;
    letter-spacing: 0.1rem;

}

.message-content {
    font-size: 1rem;
    color: #ddd;
    font-size: 2rem;

}

.special-message {
    font-weight: bold;
    color: #ff0000;
    text-shadow: 0 0 10px #ff0000;
}

.timestamp {
    font-size: 0.8rem;
    color: #777;
}

.message-input {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}

textarea {
    width: 100%;
    background: #333;
    color: #fff;
    border: 2px solid #d10000;
    padding: 10px;
    border-radius: 5px;
    font-family: inherit;
    font-size: 1rem;
    letter-spacing: 2px;

}

.send-button {
    background: #d10000;
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    text-transform: uppercase;
    transition: background 0.3s;
    font-size: 1.5rem;
    letter-spacing: 3px;
}

.send-button:hover {
    background: #ff0000;
    box-shadow: 0 0 10px #ff0000;
}
</style>
