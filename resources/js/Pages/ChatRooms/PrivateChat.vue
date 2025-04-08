<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import { playSound } from '@/utils/sounds';

const { props } = usePage();
const chat = ref(props.chat);
const otherUser = ref(props.otherUser);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(true);
const error = ref(null);

const currentUserName = computed(() => props.auth.user.name);

const senderColors = [
    '#ff5733', '#33ff57', '#3357ff',
    '#ff33a1', '#a133ff', '#33fff6',
    '#ffc733', '#ff3333'
];
const getSenderColor = sender => {
    let hash = 0;
    for (let i = 0; i < sender.length; i++) {
        hash = sender.charCodeAt(i) + ((hash << 5) - hash);
    }
    return senderColors[Math.abs(hash) % senderColors.length];
};

const fetchMessages = async () => {
    try {
        const { data } = await axios.get(`/private/get/${chat.value.id}`);
        // Map the response data to match expected structure
        messages.value = data.map(msg => ({
            id: msg.id,
            sender: msg.sender_name, // Use server-provided name
            content: msg.content,
            created_at: msg.created_at
        }));
    } catch (e) {
        console.error(e);
        error.value = 'Failed to load messages';
    } finally {
        loading.value = false;
        scrollToBottom();
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    try {
        await axios.post('/private/message/send', {
            chat_id: chat.value.id,
            message: newMessage.value
        });
        newMessage.value = '';
        playSound('message_sent');
    } catch (e) {
        console.error(e);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        const c = document.querySelector('#private-chat-container');
        if (c) {
            requestAnimationFrame(() => {
                c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' });
            });
        }
    });
};

onMounted(async () => {

    if (!chat.value?.id) {
        error.value = 'Chat session failed to initialize';
        loading.value = false;
        return;
    }

    window.Echo.private(`chat.${chat.value.id}`)
        .listen('PrivateMessageSent', evt => {
            messages.value.push({
                id: evt.id,
                sender: evt.sender_name,
                content: evt.content,
                created_at: evt.created_at
            });
            playSound('message_received');
            scrollToBottom();
        });

    await fetchMessages();
});
</script>

<template>

    <Head :title="`Chat with ${otherUser.name}`" />

    <div class="chat-room">
        <main class="chat-container">
            <div class="room-header">
                <h1>Chat with {{ otherUser.name }}</h1>
            </div>

            <div id="private-chat-container" class="chat-messages">
                <div v-if="loading" class="loading">Loading conversation...</div>
                <div v-else-if="error" class="error">{{ error }}</div>
                <div v-else>
                    <div v-for="msg in messages" :key="msg.id"
                        :class="['message', msg.sender === currentUserName ? 'mine' : 'theirs']">
                        <div class="sender" :style="{
                            color: getSenderColor(msg.sender),
                            marginLeft: msg.sender === currentUserName ? '0' : '5px'
                        }">
                            {{ msg.sender }}
                        </div>
                        <div class="message-content">{{ msg.content }}</div>
                        <div class="timestamp">{{ new Date(msg.created_at).toLocaleTimeString() }}</div>
                    </div>
                </div>
            </div>

            <div class="message-input">
                <textarea v-model="newMessage" placeholder="Type your message..."
                    @keydown.enter.exact.prevent="sendMessage"
                    @keydown.shift.enter.prevent="newMessage += '\n'"></textarea>
                <button class="send-button" @click="sendMessage">Send</button>
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
    width: 90%;
    max-width: 600px;
    display: flex;
    flex-direction: column;
    height: 90vh;
    background: #111;
    padding: 20px;
    border: 2px solid #d10000;
    box-shadow: 0 0 20px #d10000;
    position: relative;
}

.room-header {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    background: #222;
    padding: 10px;
    border-radius: 5px;
}

.message {
    margin-bottom: 12px;
    padding: 8px 12px;
    border-radius: 5px;
    position: relative;
    max-width: 80%;
}

.message.mine {
    margin-left: auto;
    background: #1a1a1a;
    border-left: 3px solid #33ff57;
}

.message.theirs {
    margin-right: auto;
    background: #1a1a1a;
    border-left: 3px solid #ff5733;
}

.sender {
    font-weight: bold;
    margin-bottom: 4px;
}

.message-content {
    font-size: 1rem;
    color: #ddd;
}

.timestamp {
    font-size: 0.7rem;
    color: #777;
    text-align: right;
    margin-top: 4px;
}

.message-input {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

textarea {
    flex: 1;
    background: #333;
    color: #fff;
    border: 2px solid #d10000;
    padding: 8px;
    border-radius: 5px;
    font-family: inherit;
    font-size: 1rem;
    resize: none;
    min-height: 50px;
}

.send-button {
    background: #d10000;
    color: #fff;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    font-weight: bold;
    text-transform: uppercase;
    transition: background 0.3s;
}

.send-button:hover {
    background: #ff0000;
    box-shadow: 0 0 10px #ff0000;
}

.loading,
.error {
    color: #aaa;
    text-align: center;
    margin-top: 20px;
}
</style>
