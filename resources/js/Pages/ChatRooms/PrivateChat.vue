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
const mediaInput = ref(null);

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
        messages.value = data.map(msg => ({
            id: msg.id,
            sender: msg.sender_name,
            content: msg.content,
            created_at: msg.created_at,
            media_path: msg.media_path,
            media_type: msg.media_type
        }));
    } catch (e) {
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

const handleMediaUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('media', file);
    formData.append('chat_id', chat.value.id);

    try {
        await axios.post('/private/message/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    } catch (error) {
        let errorMessage = 'Failed to share media';
        if (error.response?.status === 422) {
            errorMessage = error.response.data?.errors?.media?.[0] || 'Invalid file type';
        } else if (error.response?.status === 413) {
            errorMessage = 'File too large (max 25MB)';
        } else if (!error.response) {
            errorMessage = 'Network error';
        }
        alert(`Media upload failed: ${errorMessage}`);
    } finally {
        mediaInput.value.value = null;
    }
};

const triggerMediaUpload = () => {
    mediaInput.value.click();
};

const scrollToBottom = () => {
    nextTick(() => {
        const c = document.querySelector('#private-chat-container');
        if (c) c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' });
    });
};

onMounted(async () => {
    if (!chat.value?.id) {
        error.value = 'Chat session failed';
        loading.value = false;
        return;
    }

    window.Echo.private(`chat.${chat.value.id}`)
        .listen('PrivateMessageSent', evt => {
            messages.value.push({
                id: evt.id,
                sender: evt.sender_name,
                content: evt.content,
                created_at: evt.created_at,
                media_path: evt.media_path,
                media_type: evt.media_type
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
                        <div class="sender" :style="{ color: getSenderColor(msg.sender) }">
                            {{ msg.sender }}
                        </div>
                        <div class="message-content">
                            <div v-if="msg.media_path" class="media-container">
                                <img v-if="msg.media_type?.startsWith('image')" :src="`/storage/${msg.media_path}`"
                                    class="chat-media" alt="Shared media">
                                <video v-else-if="msg.media_type?.startsWith('video')" controls class="chat-media">
                                    <source :src="`/storage/${msg.media_path}`" :type="msg.media_type">
                                </video>
                            </div>
                            <span v-if="msg.content && msg.content !== 'Media shared'">
                                {{ msg.content }}
                            </span>
                        </div>
                        <div class="timestamp">{{ new Date(msg.created_at).toLocaleTimeString() }}</div>
                    </div>
                </div>
            </div>

            <div class="media-controls">
                <input type="file" ref="mediaInput" hidden @change="handleMediaUpload" accept="image/*, video/*">
                <button class="media-button" @click="triggerMediaUpload">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                        <circle cx="12" cy="13" r="4" />
                    </svg>
                </button>
                <button class="media-button" @click="Inertia.visit('/files/upload')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                </button>
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
    margin-bottom: 10px;
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

.media-container {
    margin-bottom: 8px;
}

.chat-media {
    max-width: 100%;
    max-height: 400px;
    border-radius: 5px;
    display: block;
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

.media-controls {
    display: flex;
    gap: 10px;
    padding: 5px 0;
}

.media-button {
    background: none;
    border: none;
    color: #d10000;
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.3s;
    padding: 5px;
}

.media-button:hover {
    color: #ff0000;
}

.media-button svg {
    width: 24px;
    height: 24px;
    transition: stroke 0.3s ease;
}

.media-button:hover svg {
    stroke: #ff0000;
}
</style>