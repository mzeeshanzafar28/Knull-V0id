<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';
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
const replyingTo = ref(null);
const currentUser = ref(props.auth.user);
const blockedUsers = ref([]);
const selectedMessageId = ref(null);
const mediaInput = ref(null);


playSound('chatroom');

const senderColors = [
    '#ff5733', '#33ff57', '#3357ff', '#ff33a1',
    '#a133ff', '#33fff6', '#ffc733', '#ff3333'
];

const filteredMessages = computed(() => {
    return messages.value.filter(message => {
        if (message.sender === currentUser.value.name) return true;
        const blockEntry = blockedUsers.value.find(b => b.username === message.sender);
        return !blockEntry || new Date(message.created_at) < blockEntry.blockedAt;
    });
});

const getSenderColor = (sender) => {
    let hash = 0;
    for (let i = 0; i < sender.length; i++) {
        hash = sender.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % senderColors.length;
    return senderColors[index];
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    try {
        await axios.post(`/chat/${roomId.value}/send`, {
            message: newMessage.value,
            reply_to: replyingTo.value?.id || null
        });
        newMessage.value = '';
        replyingTo.value = null;
    } catch (err) {
        console.error('Failed to send message:', err);
    }
};

const handleMediaUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('media', file);
    formData.append('message', 'Media shared');

    try {
        await axios.post(`/chat/${roomId.value}/send`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    } catch (error) {
        let errorMessage = 'Failed to share media';

        // Handle validation errors
        if (error.response?.status === 422) {
            errorMessage = error.response.data?.errors?.media?.[0] ||
                'Invalid file type or format';
        }
        // Handle payload too large
        else if (error.response?.status === 413) {
            errorMessage = 'Media file is too large (max 25MB)';
        }
        // Handle network errors
        else if (!error.response) {
            errorMessage = 'Network error - check your connection';
        }

        alert(`Media upload failed: ${errorMessage}`);
        console.error('Media upload error:', error.response?.data || error);
    } finally {
        mediaInput.value.value = null;
    }
};

const triggerMediaUpload = () => {
    mediaInput.value.click();
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

const startReply = (message) => {
    replyingTo.value = message;
    document.querySelector('textarea').focus();
};

const cancelReply = () => {
    replyingTo.value = null;
};

const findOriginalMessage = (messageId) => {
    return messages.value.find(m => m.id === messageId);
};

const toggleOptions = (messageId, event) => {
    event.stopPropagation();
    selectedMessageId.value = selectedMessageId.value === messageId ? null : messageId;
};

const sendPrivateMessage = (username) => {
    Inertia.visit(`/private/message/${username}`);
    selectedMessageId.value = null;
};

const toggleBlockUser = (username) => {
    const existingIndex = blockedUsers.value.findIndex(b => b.username === username);
    if (existingIndex > -1) {
        blockedUsers.value.splice(existingIndex, 1);
    } else {
        blockedUsers.value.push({
            username,
            blockedAt: new Date()
        });
    }
    selectedMessageId.value = null;
};

const isUserBlocked = (username) => {
    return blockedUsers.value.some(b => b.username === username);
};

onMounted(async () => {
    document.addEventListener('click', () => {
        selectedMessageId.value = null;
    });

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
            modal.innerHTML =
                `<div style="font-size: xx-large; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;">
                    <div style="background: #1a1a1a; color: #ff0000; padding: 20px; border: 2px solid #ff0000; font-family: 'Creepster', cursive; text-align: center;">
                        <h1>You can't break the protocol, you dummy!</h1>
                        <p>Join the room from the page...</p>
                        <button id="goAheadBtn" style="background: #ff0000; color: #fff; border: none; padding: 10px 20px; cursor: pointer;">OK</button>
                    </div>
                </div>`;
            document.body.appendChild(modal);

            const btn = modal.querySelector('#goAheadBtn');
            btn.addEventListener('click', () => {
                modal.remove();
                Inertia.get('/chat-rooms', {});
            });

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
                    created_at: data.timestamp,
                    reply_to: data.reply_to,
                    media_path: data.media_path,
                    media_type: data.media_type
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

setInterval(() => {
    fetchMembers();
}, 2000);

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
                    <div v-for="message in filteredMessages" :key="message.id" class="message"
                        @dblclick="startReply(message)">
                        <div class="message-options" v-if="message.sender !== currentUser.name">
                            <button @click.stop="toggleOptions(message.id, $event)" class="arrow-button">▼</button>
                            <div v-if="selectedMessageId === message.id" class="options-dropdown">
                                <button @click="sendPrivateMessage(message.sender)">Message</button>
                                <button @click="toggleBlockUser(message.sender)">
                                    {{ isUserBlocked(message.sender) ? 'Unblock' : 'Block' }}
                                </button>
                            </div>
                        </div>

                        <div v-if="message.reply_to" class="reply-preview">
                            <div class="reply-sender"
                                :style="{ color: getSenderColor(findOriginalMessage(message.reply_to)?.sender || 'Unknown') }">
                                {{ findOriginalMessage(message.reply_to)?.sender || 'Unknown' }}
                            </div>
                            <div class="reply-content">
                                {{ findOriginalMessage(message.reply_to)?.content || 'Message not available' }}
                            </div>
                        </div>

                        <!-- Updated sender div with conditional margin-left -->
                        <div class="sender" :style="{
                            color: getSenderColor(message.sender),
                            marginLeft: message.sender !== currentUser.name ? '12px' : '0'
                        }">
                            {{ message.sender }}
                        </div>

                        <div :class="['message-content', {
                            'special-message': message.content === 'Dust Cleared by Void',
                            'reply-message': message.reply_to
                        }]">
                            <div v-if="message.media_path" class="media-container">
                                <img v-if="message.media_type.startsWith('image')"
                                    :src="`/storage/${message.media_path}`" class="chat-media" alt="Shared media">
                                <video v-else-if="message.media_type.startsWith('video')" controls class="chat-media">
                                    <source :src="`/storage/${message.media_path}`" :type="message.media_type">
                                </video>
                                <div v-else class="unsupported-media">
                                    <i class="fas fa-file-download"></i>
                                    <span>Unsupported media type</span>
                                </div>
                            </div>
                            <span v-if="message.content && !message.media_path && message.content !== 'Media shared'">
                                {{ message.content }}
                            </span>
                        </div>

                        <div class="timestamp">{{ new Date(message.created_at).toLocaleTimeString() }}</div>
                    </div>
                </div>
            </div>
            <div class="reply-indicator" v-if="replyingTo">
                <div class="reply-info">
                    <span>Replying to {{ replyingTo.sender }}</span>
                    <button @click="cancelReply" class="cancel-reply">×</button>
                </div>
                <div class="reply-preview">
                    {{ replyingTo.content.length > 50
                        ? replyingTo.content.substring(0, 50) + '...'
                        : replyingTo.content
                    }}
                </div>
            </div>

            <div class="media-controls">
                <input type="file" id="media-upload" ref="mediaInput" hidden @change="handleMediaUpload"
                    accept="image/*, video/*">
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
                <textarea v-model="newMessage" placeholder="Whisper into the void..."
                    @keydown.enter.exact.prevent="sendMessage" @keydown.shift.enter="addNewLine"></textarea>
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
    display: flex;
    flex-direction: column;
    height: 95vh;
    background: #111;
    padding: 20px;
    border: 2px solid #d10000;
    box-shadow: 0 0 20px #d10000;
    position: relative;
}

.chat-messages::-webkit-scrollbar {
    display: none;
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

.description {
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
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}

.message:hover {
    background: #252525;
}

.reply-preview {
    background: #2a2a2a;
    border-left: 3px solid #ff6666;
    padding: 5px 10px;
    margin-bottom: 8px;
    border-radius: 3px;
    font-size: 0.9rem;
    color: #aaa;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    position: relative;
    padding-left: 15px;
}

.reply-preview::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ff6666;
}

.reply-sender {
    font-weight: bold;
    font-size: 0.8rem;
    margin-bottom: 3px;
}

.reply-content {
    font-size: 0.8rem;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sender {
    font-weight: bold;
    color: #ff4444;
    letter-spacing: 0.1rem;
    margin-bottom: 5px;
}

.message-content {
    font-size: 2rem;
    color: #ddd;
    margin-bottom: 5px;
}

.reply-message {
    border-left: 4px solid #ff6666;
    padding-left: 10px;
    margin-left: -10px;
}

.special-message {
    font-weight: bold;
    color: #ff0000;
    text-shadow: 0 0 10px #ff0000;
}

.timestamp {
    font-size: 0.8rem;
    color: #777;
    text-align: right;
}

.reply-indicator {
    background: #2a2a2a;
    padding: 8px 15px;
    border-left: 3px solid #ff6666;
    margin-bottom: 10px;
    border-radius: 3px;
    display: flex;
    flex-direction: column;
}

.reply-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    color: #ff6666;
    margin-bottom: 5px;
}

.cancel-reply {
    background: none;
    border: none;
    color: #ff6666;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 5px;
}

.cancel-reply:hover {
    color: #ff3333;
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
    min-height: 60px;
    resize: none;
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

.message-options {
    position: absolute;
    top: 5px;
    left: 5px;
    z-index: 1;
}

.arrow-button {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 0.8rem;
    padding: 0;
}

.options-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: #1a1a1a;
    border: 1px solid #d10000;
    border-radius: 4px;
    padding: 5px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    z-index: 2;
    min-width: 120px;
}

.options-dropdown button {
    background: #333;
    color: #fff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    white-space: nowrap;
    text-align: left;
    font-family: 'Creepster', cursive;
}

.options-dropdown button:hover {
    background: #444;
}

@media (max-height: 600px) {
    .options-dropdown {
        bottom: 100%;
        top: auto;
    }
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
}

.media-button:hover {
    color: #ff0000;
}

.chat-media {
    max-width: 100%;
    max-height: 400px;
    border-radius: 5px;
    margin-bottom: 8px;
}

.unsupported-media {
    padding: 10px;
    background: #2a2a2a;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.unsupported-media i {
    font-size: 1.2rem;
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
