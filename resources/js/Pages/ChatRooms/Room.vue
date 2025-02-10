
 <script setup>
 import { Head, usePage } from '@inertiajs/vue3';
 import { ref, onMounted } from 'vue';
 import Echo from 'laravel-echo';
//  import { useReverb } from '@inertiajs/reverb';
 import axios from 'axios';

 const { props } = usePage();
 const room = ref(props.room);
 const roomId = ref(props.roomId);
 const messages = ref([]);
 const newMessage = ref('');
 const members = ref(props.members || []);
 const error = ref(null);
//  const reverb = useReverb();

 const sendMessage = async () => {
     if (!newMessage.value.trim()) return;

     try {
         await axios.post(`/chat/${roomId.value}/send`, {
             encrypted_message: newMessage.value, // Encryption should be added here
             iv: 'IV_FROM_ENCRYPTION' // Replace with actual IV
         });
         newMessage.value = '';
     } catch (err) {
         console.error('Failed to send message:', err);
     }
 };

 // Fetch past messages
 const fetchMessages = async () => {
     try {
         const response = await axios.get(`/chat/${roomId.value}/messages`);
         messages.value = response.data;
     } catch (err) {
         console.error('Failed to fetch messages:', err);
     }
 };

 // Listen for new messages in real-time
//  reverb.channel(`chat.${roomId.value}`).listen('NewChatMessage', (event) => {
//      messages.value.push({
//          sender: event.sender,
//          message: event.message,
//          timestamp: event.timestamp
//      });
//  });

 // Fetch messages and members when mounted
 onMounted(() => {
    window.Echo.channel(`chat.${roomId.value}`)
        .listen('NewChatMessage', (event) => {
            messages.value.push({
                sender: event.sender,
                message: event.message,
                timestamp: event.timestamp
            });
        });

    fetchMessages();
});

 </script>

 <template>
     <Head :title="`Chat Room - ${room.name}`" />
     <div class="min-h-screen bg-black text-white">
         <h1 class="text-2xl font-bold">{{ room.name }}</h1>
         <p class="text-gray-400">{{ room.description }}</p>
         <p>Members: {{ members.length }} / {{ room.max_members }}</p>

         <div class="chat-box border p-4 h-80 overflow-y-auto">
             <div v-for="message in messages" :key="message.timestamp">
                 <strong>{{ message.sender }}:</strong> {{ message.message }}
             </div>
         </div>

         <div class="mt-4">
             <input
                 v-model="newMessage"
                 @keyup.enter="sendMessage"
                 class="border p-2 w-full bg-gray-800"
                 placeholder="Type your message..."
             />
             <button @click="sendMessage" class="mt-2 bg-blue-500 px-4 py-2">Send</button>
         </div>
     </div>
 </template>
